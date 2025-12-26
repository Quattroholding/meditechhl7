<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalSpeciality;
use Illuminate\Http\Request;

class MedicalSpecialityController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalSpeciality::query()
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            // Solo especialidades con al menos un practitioner con usuario activo Y suscripción activa
            ->whereHas('practitioners', function ($query) {
                $query->where('practitioners.active', true)
                    ->whereHas('user', function ($userQuery) {
                        $userQuery->where('users.active', true)
                            // Verificar que el usuario tenga cliente con suscripción activa
                            ->whereHas('clients', function ($clientQuery) {
                                $clientQuery->whereHas('subscriptions', function ($subscriptionQuery) {
                                    $subscriptionQuery->whereIn('status', ['active', 'trial'])
                                        ->where(function ($q) {
                                            $q->whereNull('current_period_ends_at')
                                                ->orWhere('current_period_ends_at', '>=', now());
                                        });
                                });
                            });
                    });
            })
            // Contar citas usando withCount para mejor performance
            ->withCount('appointments');

        // Ordenar por cantidad de citas si se solicita
        $orderBy = $request->get('order_by', 'id');
        $orderDirection = $request->get('order_direction', 'desc');

        if ($orderBy === 'appointments_count') {
            $query->orderBy('appointments_count', $orderDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        // Get current month date range for appointments count
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $specialities = $query->get()
            ->map(function ($speciality) use ($currentMonthStart, $currentMonthEnd) {
                // Obtener practitioners activos con usuarios activos Y suscripción activa
                $availablePractitioners = $speciality->practitioners()
                    ->with([
                        'user.clients.subscriptions' => function ($query) {
                            $query->whereIn('status', ['active', 'trial', 'past_due', 'suspended'])
                                ->latest();
                        },
                        'user.clients.subscriptions.package',
                    ])
                    ->withCount(['appointments as appointments_this_month' => function ($query) use ($currentMonthStart, $currentMonthEnd) {
                        $query->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);
                    }])
                    ->where('practitioners.active', true)
                    ->whereHas('user', function ($query) {
                        $query->where('users.active', true)
                            // Verificar que el usuario tenga cliente con suscripción activa
                            ->whereHas('clients', function ($clientQuery) {
                                $clientQuery->whereHas('subscriptions', function ($subscriptionQuery) {
                                    $subscriptionQuery->whereIn('status', ['active', 'trial'])
                                        ->where(function ($q) {
                                            $q->whereNull('current_period_ends_at')
                                                ->orWhere('current_period_ends_at', '>=', now());
                                        });
                                });
                            });
                    })
                    ->get(['practitioners.id', 'practitioners.name', 'practitioners.email', 'practitioners.phone', 'practitioners.user_id']);

                return [
                    'id' => $speciality->id,
                    'name' => $speciality->name,
                    'code' => $speciality->code ?? '',
                    'description' => $speciality->description ?? '',
                    'is_surgical' => $speciality->is_surgical ?? false,
                    'available_practitioners_count' => $availablePractitioners->count(),
                    'available_practitioners' => $availablePractitioners->map(function ($practitioner) {
                        $subscriptionData = null;

                        if ($practitioner->user) {
                            try {
                                $subscription = $practitioner->user->getActiveSubscription();

                                if ($subscription && $subscription->package) {
                                    $subscriptionData = [
                                        'id' => $subscription->id,
                                        'status' => $subscription->status->value,
                                        'status_label' => $subscription->status->label(),
                                        'package' => [
                                            'id' => $subscription->package->id,
                                            'name' => $subscription->package->name,
                                            'appointments_limit' => $subscription->package->appointments_limit,
                                        ],
                                        'appointments_usage' => [
                                            'limit' => $subscription->package->appointments_limit,
                                            'used' => $practitioner->user->getAppointmentsCountInCurrentPeriod(),
                                            'remaining' => $practitioner->user->getRemainingAppointments(),
                                            'is_unlimited' => $subscription->package->appointments_limit === null,
                                            'has_reached_limit' => $practitioner->user->hasReachedAppointmentsLimit(),
                                        ],
                                        'can_schedule_appointments' => $practitioner->user->canScheduleAppointments(),
                                    ];
                                }
                            } catch (\Exception $e) {
                                // If there's any error, subscription data will remain null
                            }
                        }

                        return [
                            'id' => $practitioner->id,
                            'name' => $practitioner->name,
                            'email' => $practitioner->email,
                            'phone' => $practitioner->phone,
                            'appointments_this_month' => $practitioner->appointments_this_month ?? 0,
                            'subscription' => $subscriptionData,
                        ];
                    })->values(),
                    'appointments_count' => $speciality->appointments_count,
                ];
            });

        return response()->json([
            'total' => $specialities->count(),
            'specialities' => $specialities,
        ]);
    }
}
