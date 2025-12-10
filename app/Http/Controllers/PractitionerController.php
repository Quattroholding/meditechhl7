<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use App\Models\User;
use App\Notifications\PractitionerCredentialsNotification;
use App\Services\FileService;
use App\Services\PractitionerService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PractitionerController extends Controller
{
    public function index()
    {
        $model = Practitioner::class;

        return view('practitioners.index', compact('model'));
    }

    public function create()
    {

        $clients = (auth()->user()->hasRole('admin')) ? Client::all()->pluck('long_name', 'id')->toArray() : Client::whereIn('id', auth()->user()->clients->pluck('id'))->pluck('long_name', 'id')->toArray();

        return view('practitioners.create', compact('clients'));
    }

    public function edit($id)
    {
        $data = Practitioner::find($id);
        // dd($data->specialties);
        $practitioner_clients = Client::whereId(1)->pluck('id')->toArray();
        if ($data->user) {
            $practitioner_clients = $data->user->clients->pluck('id')->toArray();
        }
        $specialties = $data->qualifications->pluck('medical_speciality_id')->toArray();
        $clients = Client::pluck('long_name', 'id')->toArray();

        // dd($qualifications, $practitioner_clients);
        return view('practitioners.edit', compact('data', 'practitioner_clients', 'specialties', 'clients'));
    }

    public function store(Request $request, PractitionerService $practitionerService)
    {

        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'identifier_type' => 'required',
            'id_number' => 'required',
            'registry' => 'required',
            'licence_code' => 'required',
            'email' => 'required|unique:practitioners',
            'gender' => 'required',
            'birth_date' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'full_phone' => 'required',
            // 'image' => 'required',
            'clients' => 'required',
        ]);

        try {

            DB::beginTransaction();

            // Crear usuario
            $model = new User;
            $model->fill($request->all());
            $model->last_name = $request->last_name;
            // Generar password temporal para enviar correo a usuario
            $temporaryPassword = Str::random(10);
            $model->password = Hash::make($temporaryPassword);
            // Manejar múltiples clientes
            $model->default_client_id = $request->clients[0];

            if ($model->save()) {
                // Sincronizar clientes
                $sync = [];
                $clients = $request->clients;
                foreach ($clients as $client) {
                    $sync[$client] = [
                        'client_id' => $client,
                        'created_at' => now()->format('Y-m-d H:i:s'),
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                        'user_id' => $model->id,
                    ];
                }
                $model->clients()->sync($sync);
                $model->assignRole('doctor');



                // Crear practitioner usando el servicio centralizado
                $practitioner = $practitionerService->createOrUpdatePractitioner(
                    $model,
                    $request->all(),
                    $request->medical_speciality ?? []
                );
            }

            // Enviar notificación con credenciales temporales
            $model->notify(new PractitionerCredentialsNotification($model, $temporaryPassword));

            DB::commit();

            session()->flash('message.success', 'Creado con exito.');

            return redirect(route('practitioner.index'));

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('message.error', $e->getMessage());

            return redirect(route('practitioner.create'))->withInput($request->all());
        }
    }

    public function update(Request $request, $id)
    {

        try {

            $validated = $request->validate([

                'id_type' => 'required',
                'id_number' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'gender' => 'required',
                'birth_date' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'registry' => 'required',
                'licence_code' => 'required',
                'address' => 'required',
                // 'full_phone' => 'required',
                // 'image' => 'required',
                'clients' => 'required',
            ]);

            $practitioner = Practitioner::findOrFail($id);

            DB::transaction(function () use ($practitioner, $request) {

                if ($practitioner->user) {
                    $practitioner->user->fill($request->all());
                    $practitioner->user->first_name = $request->first_name;
                    $practitioner->user->last_name = $request->last_name;
                    // $practitioner->user->email = $request->email;
                    $practitioner->user->save();
                    $sync = [];
                    $clients = $request->clients;
                    foreach ($clients as $client) {
                        $sync[$client] = ['client_id' => $client, 'created_at' => now()->format('Y-m-d H:i:s'), 'updated_at' => now()->format('Y-m-d H:i:s'), 'user_id' => $practitioner->user_id];
                    }
                    $practitioner->user->clients()->sync($sync);
                }

                // $model->assignRole('doctor');

                $practitioner->fill($request->except('birth_date', 'phone'));
                $prefix = 'Dr. ';
                if ($request->gender == 'female') {
                    $prefix = 'Dra. ';
                }
                $practitioner->name = $prefix.$request->first_name.' '.$request->last_name;
                $practitioner->given_name = $request->first_name;
                $practitioner->phone = $request->full_phone;
                $practitioner->family_name = $request->last_name;
                $practitioner->identifier_type = $request->id_type;
                $practitioner->registry = $request->registry;
                $practitioner->licence_code = $request->licence_code;
                $practitioner->identifier = $request->id_number;
                $fecha = DateTime::createFromFormat('d/m/Y', $request->birth_date);
                $fecha->setTime(0, 0, 0);
                $practitioner->birth_date = $fecha->format('Y-m-d H:i:s');

                if ($practitioner->save()) {
                    if ($request->has('medical_speciality')) {
                        $specialties = $request->medical_speciality;
                        $syn = [];
                        foreach ($specialties as $speciality) {
                            $medical_speciality_name = MedicalSpeciality::find($speciality);
                            $syn[$speciality] = ['code' => $speciality, 'medical_speciality_id' => $speciality, 'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'updated_at' => Carbon::now()->format('Y-m-d H:i:s'), 'practitioner_id' => $practitioner->id, 'display' => $medical_speciality_name->name];
                        }
                        $practitioner->specialties()->sync($syn);
                    }
                }

                if ($request->file('image')) {
                    $service = new FileService;
                    $data['record_id'] = $practitioner->id;
                    $data['folder'] = 'patients';
                    $data['type'] = 'avatar';
                    $service->guardarArchivos([$request->file('image')], $data);
                }
            });

            session()->flash('message.success', 'Actualizado con exito.');

        } catch (\Exception $e) {
            session()->flash('message.error', $e->getMessage());
        }

        if ($request->has('redirect')) {
            return redirect($request->redirect);
        }

        return redirect(route('practitioner.edit', $id));
    }

    public function profile(Request $request, $id)
    {
        $data = Practitioner::find($id);

        return view('practitioners.profile', compact('data'));
    }

    public function directory()
    {
        return view('practitioners.directory');
    }
}
