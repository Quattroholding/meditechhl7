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

        $client = Client::create([
            'name' => 'Soluciones Meditec',
            'ruc' => '155685992-2-2019',
            'dv' => '11',
            'long_name' => 'Soluciones Meditec',
            'email' => 'business@meditecpty.com',
            'whatsapp' => '+5078316174',
            'logo' => url('images/logoFull.png'),
        ]);

        $branch = Branch::create([
            'client_id' => $client->id,
            'name' => 'San Francisco',
            'phone' => '+507 831-6100',
            'address' => 'calle 74 San Francisco edificio Quattroholding',
            'type' => 'hospital',
            'country_id'=>1,
            'state_id'=>8,
        ]);

        ConsultingRoom::create([
            'branch_id' => $branch->id,
            'name' => 'Departamento de IT',
            'number' => '1',
            'floor' => '1',
        ]);

        //CREAR ADMINISTRADORES
        $administradores = array( 'rgasperi@smartcarebilling.com', 'atenorio@smartcarebilling.com');

        foreach ($administradores as $administrador) {
            $admin = User::factory()->create([
                'first_name' => 'Administrador',
                'last_name' => 'Del Sistema',
                'email' => $administrador,
                'password' => 'Prueba.1',
                'first_login_at'=>now(),
            ]);

            $admin->assignRole('admin');
            $admin->default_client_id = $client->id;
            $admin->save();

            UserClient::create([
                'client_id' => $client->id,
                'user_id' => $admin->id,
            ]);
        }
    }
}
