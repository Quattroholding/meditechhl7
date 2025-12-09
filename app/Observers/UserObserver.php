<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserWidgetPreference;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->createDefaultWidgetPreferences($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if roles have changed and update widget preferences accordingly
        if ($user->wasChanged('role_id') || $user->isDirty('role_id')) {
            $this->createDefaultWidgetPreferences($user);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Clean up widget preferences when user is deleted
        UserWidgetPreference::where('user_id', $user->id)->delete();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $this->createDefaultWidgetPreferences($user);
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        // Clean up widget preferences when user is force deleted
        UserWidgetPreference::where('user_id', $user->id)->delete();
    }

    /**
     * Create default widget preferences based on user role
     */
    private function createDefaultWidgetPreferences(User $user): void
    {
        // Get user roles
        $userRoles = $user->getRoleNames()->toArray();

        // Map roles to dashboard types
        $dashboardTypes = [];

        if (in_array('doctor', $userRoles)) {
            $dashboardTypes[] = 'doctor';
        }

        if (in_array('recepcionista', $userRoles)) {
            $dashboardTypes[] = 'recepcionist';
        }

        if (in_array('paciente', $userRoles)) {
            $dashboardTypes[] = 'patient';
        }

        // If no recognized roles, default to patient
        if (empty($dashboardTypes)) {
            $dashboardTypes[] = 'patient';
        }

        // Create default preferences for each dashboard type
        foreach ($dashboardTypes as $dashboardType) {
            $this->createWidgetPreferencesForDashboard($user->id, $dashboardType);
        }
    }

    /**
     * Create widget preferences for a specific dashboard type
     */
    private function createWidgetPreferencesForDashboard(int $userId, string $dashboardType): void
    {
        $defaultWidgets = UserWidgetPreference::getDefaultWidgets($dashboardType);

        foreach ($defaultWidgets as $widgetKey => $widgetConfig) {
            UserWidgetPreference::updateOrCreate(
                [
                    'user_id' => $userId,
                    'dashboard_type' => $dashboardType,
                    'widget_name' => $widgetKey,
                ],
                [
                    'widget_description' => $widgetConfig['description'] ?? $widgetConfig['name'],
                    'is_visible' => true, // All widgets visible by default
                    'order_position' => $widgetConfig['order'],
                    'width' => $widgetConfig['width'] ?? 'col-lg-6',
                ]
            );
        }
    }
}
