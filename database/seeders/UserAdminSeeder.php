<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Client;
use App\Models\ConsultingRoom;
use App\Models\User;
use App\Models\UserClient;
use Illuminate\Database\Seeder;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'first_name' => 'Administrador',
            'last_name' => 'Del Sistema',
            'email' => 'rgasperi@smartcarebilling.com',
            'password' => 'Prueba.1',
            'first_login_at'=>now(),
        ]);

        $admin->assignRole('admin');

        $client = Client::create([
            'name' => 'Soluciones Meditec',
            'ruc' => '155685992-2-2019',
            'dv' => '11',
            'long_name' => 'Soluciones Meditec',
            'email' => 'business@meditecpty.com',
            'whatsapp' => '0800-555-555',
            // 'logo' => fake()->imageUrl(),
        ]);

        $admin->default_client_id = $client->id;
        $admin->save();

        $branch = Branch::create([
            'client_id' => $client->id,
            'name' => 'Cuartel Central',
            'phone' => '0800-555-555',
            'address' => 'calle 74 San Francisco edificio Quattroholding',
            'type' => 'hospital',
        ]);

        ConsultingRoom::create([
            'branch_id' => $branch->id,
            'name' => 'Departamento de IT',
            'number' => '1',
            'floor' => '1',
        ]);

        UserClient::create([
            'client_id' => $client->id,
            'user_id' => $admin->id,
        ]);

    }
}
