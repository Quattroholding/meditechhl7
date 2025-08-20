<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Insurance;
use Illuminate\Database\Seeder;

class InsuranceCompanySeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los clientes para asignar las aseguradoras
        $clients = Client::whereId(1)->get();

        if ($clients->isEmpty()) {
            $this->command->error('No hay clientes en la base de datos. Ejecuta primero el seeder de clientes.');

            return;
        }

        // Lista completa de aseguradoras de Panamá según superseguros.gob.pa
        $insuranceCompanies = [
            [
                'name' => 'FMP',
                'code' => 'VA',
                'phone' => '800-669-8477',
                'contact_phone' => '800-669-8477',
                'address' => '',
                'contact_person' => '',
                'website' => 'www.va.gov',
                'coverage_types' => ['Personas', 'General', 'Fianza'],
                'notes' => '',
            ],
            [
                'name' => 'TRICARE',
                'code' => 'TRICARE',
                'phone' => '800-444-5445',
                'contact_phone' => '800-444-5445',
                'address' => '7700 Arlington BoulevardSuite 5101 Falls Church, VA 22042-5101',
                'contact_person' => '',
                'website' => 'tricare.mil',
                'coverage_types' => ['Personas', 'General', 'Fianza'],
                'notes' => '',
            ],
            [
                'name' => 'Acerta Compañía de Seguros, S.A.',
                'code' => 'ACERTA',
                'phone' => '(507) 307-3000',
                'contact_phone' => '(507) 307-3001',
                'address' => 'Santa María Business District, Boulevard Santa María, #72 Torre 1, Panamá',
                'contact_person' => 'Carlos Manuel Tribaldos',
                'website' => 'www.acertaseguros.com',
                'coverage_types' => ['Personas', 'General', 'Fianza'],
                'notes' => 'Gerente General: Carlos Manuel Tribaldos',
            ],
            [
                'name' => 'Aliado Seguros, S.A.',
                'code' => 'ALIADO',
                'phone' => '(507) 304-8555',
                'address' => 'Edificio Banco Aliado, calle 50 y 56, Piso 4',
                'contact_person' => 'Gina E. Herrero Ch.',
                'website' => 'https://www.aliadoseguros.com/',
                'coverage_types' => ['Personas', 'General', 'Fianzas'],
                'notes' => 'Gerente General: Gina E. Herrero Ch.',
            ],
            [
                'name' => 'Aseguradora Ancón, S.A.',
                'code' => 'ANCON',
                'phone' => '(507) 210-8700',
                'contact_phone' => '(507) 210-8799',
                'address' => 'Torre Aseguradora Ancon, Avenida Centenario, Costa del Este',
                'contact_person' => 'Wilson Roberto Espinoza',
                'website' => 'https://www.asegurancon.com/Ancon/',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas', 'Reaseguros'],
                'notes' => 'Gerente General: Wilson Roberto Espinoza. Licencia: General de Reaseguros',
            ],
            [
                'name' => 'Aseguradora Global, S.A.',
                'code' => 'GLOBAL',
                'phone' => '(507) 206-2021',
                'contact_phone' => '(507) 203-2076',
                'address' => 'Calle 50, Torre Global Bank, Piso 26',
                'contact_person' => 'Andrés Correa Gómez',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas', 'Reaseguros'],
                'notes' => 'Gerente General: Andrés Correa Gómez. Licencia: General de Reaseguros',
            ],
            [
                'name' => 'Assa Compañía de Seguros, S.A.',
                'code' => 'ASSA',
                'phone' => '(507) 265-7777',
                'address' => 'Avenida Balboa, Edificio Bay Mall, Torre Norte, Piso 18',
                'contact_person' => 'Luis Eduardo Batista',
                'website' => 'https://www.assaseguros.com/',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas'],
                'notes' => 'Gerente General: Luis Eduardo Batista',
            ],
            [
                'name' => 'AIG Seguros (Panamá), S.A.',
                'code' => 'AIG',
                'phone' => '(507) 297-0077',
                'address' => 'Torre de las Américas, Punta Pacífica, Piso 51',
                'contact_person' => 'Eduardo A. Ehrman M.',
                'website' => 'https://www.aig.com.pa/',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas'],
                'notes' => 'Gerente General: Eduardo A. Ehrman M.',
            ],
            [
                'name' => 'Banistmo Seguros, S.A.',
                'code' => 'BANISTMO',
                'phone' => '(507) 206-4100',
                'address' => 'Torre Banistmo, Marbella, Piso 24',
                'contact_person' => 'Gustavo A. Vollmer G.',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas'],
                'notes' => 'Gerente General: Gustavo A. Vollmer G.',
            ],
            [
                'name' => 'BUPA Panamá, S.A.',
                'code' => 'BUPA',
                'phone' => '(507) 294-1444',
                'address' => 'Torre BCT, Avenida Balboa, Piso 17',
                'website' => 'https://www.bupa.com.pa/',
                'coverage_types' => ['Personas', 'Salud'],
                'notes' => 'Especializada en seguros de salud',
            ],
            [
                'name' => 'Fedpa Seguros, S.A.',
                'code' => 'FEDPA',
                'phone' => '(507) 264-3186',
                'address' => 'Vía España, Edificio Plaza Obarrio, Torre A, Piso 12',
                'contact_person' => 'Ricardo A. Pérez',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas'],
                'notes' => 'Gerente General: Ricardo A. Pérez',
            ],
            [
                'name' => 'General de Seguros, S.A.',
                'code' => 'GENERAL',
                'phone' => '(507) 269-2255',
                'address' => 'Calle Elvira Méndez, Edificio General de Seguros',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas'],
                'notes' => 'Una de las aseguradoras más antiguas de Panamá',
            ],
            [
                'name' => 'MAPFRE Panamá, S.A.',
                'code' => 'MAPFRE',
                'phone' => '(507) 300-5800',
                'address' => 'Plaza MAPFRE, Calle 50, Obarrio',
                'website' => 'https://www.mapfre.com.pa/',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas', 'Reaseguros'],
                'notes' => 'Parte del grupo español MAPFRE',
            ],
            [
                'name' => 'Multiseguros, S.A.',
                'code' => 'MULTI',
                'phone' => '(507) 264-6363',
                'address' => 'Calle 50, Edificio Oceania Business Plaza, Torre 2000, Piso 41',
                'website' => 'https://www.multiseguros.com/',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas'],
                'notes' => 'Aseguradora local con amplia experiencia',
            ],
            [
                'name' => 'Pan-American Life Insurance de Panamá, S.A.',
                'code' => 'PANAM',
                'phone' => '(507) 269-4144',
                'address' => 'Torre de las Américas, Punta Pacífica, Piso 41',
                'website' => 'https://www.palic.com/',
                'coverage_types' => ['Personas', 'Vida'],
                'notes' => 'Especializada en seguros de vida y personas',
            ],
            [
                'name' => 'Sancor Seguros Panamá, S.A.',
                'code' => 'SANCOR',
                'phone' => '(507) 300-4700',
                'address' => 'World Trade Center, Torre Norte, Piso 13',
                'website' => 'https://www.sancorseguros.com.pa/',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas'],
                'notes' => 'Parte del grupo argentino Sancor Seguros',
            ],
            [
                'name' => 'Seguros Sagicor, S.A.',
                'code' => 'SAGICOR',
                'phone' => '(507) 265-3636',
                'address' => 'Torre Swiss Bank, Calle 53 Este, Marbella, Piso 16',
                'website' => 'https://www.sagicor.com.pa/',
                'coverage_types' => ['Personas', 'Vida', 'Salud'],
                'notes' => 'Especializada en seguros de vida y salud',
            ],
            [
                'name' => 'Seguros Suramericana, S.A.',
                'code' => 'SURA',
                'phone' => '(507) 340-1200',
                'address' => 'Torre Suramericana, Costa del Este, Avenida Centenario',
                'website' => 'https://www.segurossura.com.pa/',
                'coverage_types' => ['Personas', 'Generales', 'Fianzas'],
                'notes' => 'Parte del grupo colombiano Suramericana',
            ],
            [
                'name' => 'Vivir Seguros, S.A.',
                'code' => 'VIVIR',
                'phone' => '(507) 396-5000',
                'address' => 'Torre Financial Park, Avenida Centenario, Costa del Este, Piso 18',
                'website' => 'https://www.vivirseguros.com/',
                'coverage_types' => ['Personas', 'Vida', 'Salud'],
                'notes' => 'Especializada en seguros de vida y salud',
            ],
        ];

        // Para cada cliente, crear las aseguradoras
        foreach ($clients as $client) {
            $this->command->info("Creando aseguradoras para el cliente: {$client->name}");

            foreach ($insuranceCompanies as $insuranceData) {
                // Verificar si ya existe esta aseguradora para este cliente
                $exists = Insurance::where('client_id', $client->id)
                    ->where('code', $insuranceData['code'].'_'.$client->id)
                    ->exists();

                if (! $exists) {
                    $notes = $insuranceData['notes'] ?? '';
                    if (isset($insuranceData['website'])) {
                        $notes .= ($notes ? "\n" : '').'Website: '.$insuranceData['website'];
                    }

                    Insurance::create([
                        'client_id' => $client->id,
                        'name' => $insuranceData['name'],
                        'code' => $insuranceData['code'].'_'.$client->id,
                        'phone' => $insuranceData['phone'] ?? null,
                        'contact_phone' => $insuranceData['contact_phone'] ?? null,
                        'address' => $insuranceData['address'] ?? null,
                        'contact_person' => $insuranceData['contact_person'] ?? null,
                        'coverage_types' => $insuranceData['coverage_types'] ?? null,
                        'notes' => $notes ?: null,
                        'is_active' => true,
                        'default_coverage_percentage' => 80.00, // 80% de cobertura por defecto
                        'default_copay_amount' => 25.00, // $25 de copago por defecto
                    ]);

                    $this->command->info("  ✓ {$insuranceData['name']}");
                } else {
                    $this->command->warn("  - {$insuranceData['name']} ya existe para este cliente");
                }
            }
        }

        $this->command->info("\n✅ Seeder de Compañías de Seguros de Panamá completado exitosamente!");
        $this->command->info('Total de aseguradoras: '.count($insuranceCompanies));
        $this->command->info('Fuente: Superintendencia de Seguros y Reaseguros de Panamá');
    }
}
