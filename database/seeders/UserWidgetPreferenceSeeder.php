<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserWidgetPreference;
use Illuminate\Database\Seeder;

class UserWidgetPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dashboardTypes = ['doctor', 'assistant', 'patient'];
        $roleMapping = [
            'doctor' => 'doctor',
            'assistant' => 'asistente',
            'patient' => 'paciente',
        ];

        foreach ($dashboardTypes as $dashboardType) {
            $roleName = $roleMapping[$dashboardType];

            // Get users with the corresponding role
            $users = User::whereHas('roles', function ($query) use ($roleName) {
                $query->where('name', $roleName);
            })->get();

            $defaultWidgets = UserWidgetPreference::getDefaultWidgets($dashboardType);

            foreach ($users as $user) {
                foreach ($defaultWidgets as $widgetKey => $widgetData) {
                    UserWidgetPreference::firstOrCreate([
                        'user_id' => $user->id,
                        'dashboard_type' => $dashboardType,
                        'widget_name' => $widgetKey,
                    ], [
                        'is_visible' => true,
                        'order_position' => $widgetData['order'],
                        'widget_description' => $widgetData['description'],
                        'width' => $widgetData['width'],
                    ]);
                }
            }

            $this->command->info('Created default widget preferences for '.$users->count().' '.$dashboardType.' users');
        }
    }
}
