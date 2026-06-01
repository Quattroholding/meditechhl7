<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Client;
use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use App\Models\Practitioner;
use App\Models\Rol;
use App\Models\User;
use App\Models\UserClient;
use App\Notifications\PractitionerCredentialsNotification;
use App\Services\FileService;
use App\Services\PractitionerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {

        $model = User::class;

        return view('users.index', compact('model'));
    }

    public function create()
    {

        $selected_clients = UserClient::whereUserId(auth()->user()->id)->pluck('client_id')->toArray();

        if (auth()->user()->can('clientes')) {
            $clients = Client::pluck('name', 'id')->toArray();
        } else {
            $clients = Client::whereIn('id', UserClient::whereUserId(auth()->user()->id)->pluck('client_id'))->pluck('name', 'id')->toArray();
        }

        return view('users.create', compact('clients', 'selected_clients'));
    }

    public function store(UserFormRequest $request, PractitionerService $practitionerService)
    {

        try {
            DB::beginTransaction();

            if (! auth()->user()->can('create', auth()->user())) {
                throw new \ErrorException(__('user.plan_error_message'));
            }

            $model = new User;
            $model->fill($request->all());
            $model->created_by = auth()->user()->id;
            $temporaryPassword = $request->password;
            $model->default_client_id = $request->clients[0] ?? auth()->user()->default_client_id;
            // SE ASIGNA EL ROL SEGÚN EL ID
            $rol = Rol::find($request->rol);

            if ($model->save() && $rol) {

                $model->assignRole($rol->name);
                // SE ASOCIA EL USUARIO CON EL CLIENTE QUE SELECCIONÓ EN EL FORMULARIO

                if ($request->has('clients')) {
                    $model->clients()->sync($request->get('clients'));
                }

                // Si es doctor o asistente médico, crear o asociar practitioner
                if (in_array($rol->name, ['doctor', 'asistente medico'])) {
                    // Buscar practitioner existente por email
                    $practitioner = Practitioner::whereEmail($request->email)->first();

                    if ($practitioner) {
                        // Si existe, solo actualizar el user_id
                        $practitioner->user_id = $model->id;
                        $practitioner->save();
                    } else {
                        // Si no existe, crear uno nuevo usando el servicio
                        $practitioner = $practitionerService->createOrUpdatePractitioner(
                            $model,
                            $request->all(),
                            $request->medical_speciality ?? []
                        );
                    }
                }

                if ($request->file('avatar')) {
                    // SE GUARDA EL AVATAR EN LA TABLA DE ARCHIVOS
                    $service = new FileService;
                    $filename = 'profile_picture_'.$model->id;
                    $model->profile_picture = $service->uploadSingleFile($request->file('avatar'), 'users', $filename);
                    $model->save();
                }

                // cargar plantilla de consulta
                $encounter_sections = EncounterSection::whereNull('medical_speciality_id');

                if ($rol->name == 'asistente medico') {
                    $encounter_sections->where('available_for_medical_assistant', true);
                }

                foreach ($encounter_sections->get() as $es) {
                    EncounterTemplate::firstOrCreate([
                        'type' => 'client',
                        'client_id' => $model->default_client_id,
                        'user_id' => $model->id,
                        'encounter_section_id' => $es->id,
                    ]);
                }

                $model->notify(new PractitionerCredentialsNotification($model, $temporaryPassword));

                DB::commit();

                $request->session()->flash('message.success', 'Usuario '.$rol->name.' registrado con éxito.');

                return redirect(route('user.index'));
            } else {
                $request->session()->flash('message.success', 'Hubo un error y no se pudo crear.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $request->session()->flash('message.error', $e->getMessage());

            return redirect(route('user.create'))->withInput();
        }

        return redirect(route('user.create'));
    }

    public function edit($id)
    {

        $data = User::find($id);

        if (auth()->user()->hasRole('recepcionista') && $id != auth()->user()->id) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        return view('users.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        try {
            $user = User::find($id);
            $fields = $request->except('id');
            if (empty($request->password)) {
                unset($fields['password']);
            }
            $user->fill($fields);

            if ($request->has('clients')) {
                $user->clients()->sync($request->get('clients'));
            }

            if ($request->file('avatar')) {
                $service = new FileService;
                $filename = 'profile_picture_'.$user->id;
                $user->profile_picture = $service->uploadSingleFile($request->file('avatar'), 'users', $filename);
            }
            $user->default_client_id = $request->clients[0] ?? 1;

            if ($user->save()) {

                $request->session()->flash('message.success', 'Actualizado con exito');
            } else {
                $request->session()->flash('message.error', '¡Error!, este usuario no se puede actualizar.');
            }

        } catch (\Exception $e) {

            $request->session()->flash('message.error', $e->getMessage());

            return redirect(route('user.edit', $id))->withInput();
        }

        return redirect(route('user.edit', $id));
    }

    public function destroy($id)
    {

        /*if(auth()->user()->id ==$id){
            session()->flash('message.error','Este usuario no se puede borrar.');
            return redirect()->back();
        }
        $data = User::find($id);
        $data->delete();*/
        $user = User::find($id);
        $user->deactivate();
        session()->flash('message.succes', 'usuario desactivado correctamente');

        return redirect(route('user.index'));
    }

    public function activate(User $user)
    {
        $user = User::find($id);
        $user->activate();
        session()->flash('message.succes', 'usuario activado correctamente');

        return redirect(route('user.index'));
    }

    public function changeClient($client_id)
    {

        session(['client_'.auth()->user()->id => Client::find($client_id)]);

        session()->flash('message.succes', 'Client Actualizado con exito.');

        return back();
    }
}
