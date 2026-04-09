<?php

namespace App\Http\Controllers;

use App\Enums\DiscountSource;
use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use App\Models\Package;
use App\Models\Practitioner;
use App\Models\User;
use App\Models\UserClient;
use App\Notifications\PractitionerCredentialsNotification;
use App\Services\DiscountService;
use App\Services\FileService;
use App\Services\PractitionerService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    //
    public function index()
    {
        $model = Client::class;

        return view('clients.index', compact('model'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(
        StoreClientRequest $request,
        SubscriptionService $subscriptionService,
        DiscountService $discountService,
        FileService $fileService,
        PractitionerService $practitionerService,
    ) {
        try {
            return DB::transaction(function () use ($request, $subscriptionService, $discountService, $fileService, $practitionerService) {
                // Crear el cliente
                $client = new Client;
                $client->dv = $request->dv;
                $client->ruc = $request->ruc;
                $client->long_name = $request->long_name;
                $client->name = $request->name;
                $client->email = $request->email;
                $client->whatsapp = $request->phone;
                $client->active = 1;
                $client->package_id = $request->package_id;
                $client->save();

                // Obtener el paquete
                $package = Package::findOrFail($request->package_id);

                // Crear el usuario administrador del cliente
                $user = new User;
                $user->last_name = $request->last_name ?? $request->name;
                $user->first_name = $request->first_name ?? 'Admin de';
                $user->email = $request->email;
                $user->password = $request->password;
                $user->profile_picture = null;
                $user->default_client_id = $client->id;
                $user->assignRole('admin client');
                $user->save();

                // Crear la relación usuario-cliente
                $userClient = new UserClient;
                $userClient->user_id = $user->id;
                $userClient->client_id = $client->id;
                $userClient->save();

                // Subir el logo si existe
                if ($request->hasFile('logo')) {
                    $filename = 'logo_'.time();
                    $client->logo = $fileService->uploadSingleFile($request->file('logo'), 'clients', $filename);
                    $user->profile_picture = $client->logo;
                    $client->save();
                    $user->save();
                }

                // Crear Practitioner si el paquete tiene max_doctors=1
                if ($package->max_doctors_included === 1 && $request->filled('identifier')) {
                    // Crear practitioner usando el servicio centralizado
                    $practitioner = $practitionerService->createOrUpdatePractitioner(
                        $user,
                        $request->all(),
                        $request->medical_speciality ?? []
                    );

                    $user->assignRole('doctor');
                    $user->removeRole('admin client');
                    Log::info('Practitioner creado para cliente con paquete max_users=1', [
                        'client_id' => $client->id,
                        'practitioner_id' => $practitioner->id,
                        'user_id' => $user->id,
                    ]);

                    // Enviar notificación con credenciales temporales
                    $user->notify(new PractitionerCredentialsNotification($user, $request->password));
                }

                // Preparar opciones de suscripción
                $subscriptionOptions = [
                    'trial_days' => $request->trial_days ?? 0,
                    'free_months' => $request->free_months ?? 0,
                    'extra_doctors' => $request->extra_doctors ?? 0,
                    'billing_day' => $request->billing_day,
                    'referral_code' => $request->referral_code,
                ];

                // Crear la suscripción
                $subscription = $subscriptionService->create($client, $package, $subscriptionOptions);

                // Aplicar descuento personalizado si existe
                if ($request->filled('custom_discount_type') && $request->filled('custom_discount_value')) {
                    $discountService->create($client, [
                        'subscription_id' => $subscription->id,
                        'discount_type' => $request->custom_discount_type,
                        'discount_value' => $request->custom_discount_value,
                        'reason' => $request->custom_discount_reason ?? 'Descuento personalizado al crear cuenta',
                        'source' => DiscountSource::PROMOTION->value,
                        'applies_to_invoices' => $request->custom_discount_invoices ?? 1,
                        'is_active' => true,
                    ]);
                }

                $practitionerCreated = $package->max_doctors_included === 1 && $request->filled('practitioner_identifier');

                Log::info('Cliente creado exitosamente', [
                    'client_id' => $client->id,
                    'subscription_id' => $subscription->id,
                    'package_id' => $package->id,
                    'has_trial' => $subscription->trial_ends_at !== null,
                    'has_custom_discount' => $request->filled('custom_discount_type'),
                    'practitioner_created' => $practitionerCreated,
                ]);

                $successMessage = 'Cliente registrado con éxito. ';

                if ($practitionerCreated) {
                    $successMessage .= 'Doctor creado correctamente. ';
                }

                $successMessage .= ($subscription->status->value === 'pending_activation'
                    ? 'Se ha generado la primera factura que debe ser pagada para activar la suscripción.'
                    : 'La suscripción ha sido configurada correctamente.');

                session()->flash('message.success', $successMessage);

                return redirect(route('client.index'));
            });
        } catch (\Exception $e) {
            Log::error('Error al crear cliente', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('message.error', 'Hubo un error al crear el cliente: '.$e->getMessage());

            return redirect(route('client.create'))->withInput();
        }
    }

    public function edit($id)
    {

        $data = Client::find($id);

        return view('clients.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        try {
            $validated = $request->validate([
                'name' => 'required',
                'long_name' => 'required',
                'ruc' => 'required',
                'email' => 'required',
                'dv' => 'required',
                'whatsapp' => 'required',
                'package_id' => 'required',
                // 'full_phone' => 'required',
                // 'logo' => 'required',
            ]);

            $model = Client::find($id);
            $model->fill($request->all());

            if ($model->save()) {

                if ($request->file('logo')) {
                    $service = new FileService;
                    $filename = 'client_logo_'.$model->id;
                    $model->logo = $service->uploadSingleFile($request->file('logo'), 'clients', $filename);
                    $model->save();
                }
                $request->session()->flash('message.success', 'Actualización con exito.');
            } else {
                $request->session()->flash('message.success', 'Hubo un error y no se pudo actualizar.');
            }

        } catch (\Exception $e) {
            $request->session()->flash('message.error', $e->getMessage());
        }

        return redirect(route('client.edit', $id));
    }

    public function destroy($id)
    {

        $data = Client::find($id);
        $data->delete();

        return redirect(route('client.index'));
    }

    public function getReferralCode()
    {
        return view('clients.referral-code');
    }
}
