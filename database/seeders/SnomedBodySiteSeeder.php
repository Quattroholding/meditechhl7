<?php

namespace Database\Seeders;

use App\Models\SnomedBodySite;
use Illuminate\Database\Seeder;

class SnomedBodySiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bodySites = [
            // Cabeza y Cuello
            ['snomed_code' => '39937001', 'display' => 'Skin structure', 'display_es' => 'Piel (estructura general)', 'category' => 'General', 'sort_order' => 1],
            ['snomed_code' => '113305005', 'display' => 'Skin structure of face', 'display_es' => 'Piel de la cara', 'category' => 'Cabeza', 'sort_order' => 10],
            ['snomed_code' => '361355005', 'display' => 'Skin structure of forehead', 'display_es' => 'Piel de la frente', 'category' => 'Cabeza', 'sort_order' => 11],
            ['snomed_code' => '116888000', 'display' => 'Skin structure of nose', 'display_es' => 'Piel de la nariz', 'category' => 'Cabeza', 'sort_order' => 12],
            ['snomed_code' => '38481008', 'display' => 'Skin structure of cheek', 'display_es' => 'Piel de la mejilla', 'category' => 'Cabeza', 'sort_order' => 13],
            ['snomed_code' => '15776009', 'display' => 'Skin structure of chin', 'display_es' => 'Piel del mentón', 'category' => 'Cabeza', 'sort_order' => 14],
            ['snomed_code' => '89837001', 'display' => 'Skin structure of ear', 'display_es' => 'Piel de la oreja', 'category' => 'Cabeza', 'sort_order' => 15],
            ['snomed_code' => '362103001', 'display' => 'Skin structure of eyelid', 'display_es' => 'Piel del párpado', 'category' => 'Cabeza', 'sort_order' => 16],
            ['snomed_code' => '45048000', 'display' => 'Skin structure of neck', 'display_es' => 'Piel del cuello', 'category' => 'Cabeza', 'sort_order' => 17],
            ['snomed_code' => '41111004', 'display' => 'Skin structure of scalp', 'display_es' => 'Piel del cuero cabelludo', 'category' => 'Cabeza', 'sort_order' => 18],

            // Tronco
            ['snomed_code' => '53120007', 'display' => 'Skin structure of trunk', 'display_es' => 'Piel del tronco', 'category' => 'Tronco', 'sort_order' => 20],
            ['snomed_code' => '118878009', 'display' => 'Skin structure of chest', 'display_es' => 'Piel del pecho', 'category' => 'Tronco', 'sort_order' => 21],
            ['snomed_code' => '48075008', 'display' => 'Skin structure of abdomen', 'display_es' => 'Piel del abdomen', 'category' => 'Tronco', 'sort_order' => 22],
            ['snomed_code' => '70323009', 'display' => 'Skin structure of back', 'display_es' => 'Piel de la espalda', 'category' => 'Tronco', 'sort_order' => 23],
            ['snomed_code' => '362080001', 'display' => 'Skin structure of upper back', 'display_es' => 'Piel de la espalda superior', 'category' => 'Tronco', 'sort_order' => 24],
            ['snomed_code' => '362081002', 'display' => 'Skin structure of lower back', 'display_es' => 'Piel de la espalda inferior', 'category' => 'Tronco', 'sort_order' => 25],
            ['snomed_code' => '12921003', 'display' => 'Skin structure of breast', 'display_es' => 'Piel de la mama', 'category' => 'Tronco', 'sort_order' => 26],
            ['snomed_code' => '81745001', 'display' => 'Skin structure of axilla', 'display_es' => 'Piel de la axila', 'category' => 'Tronco', 'sort_order' => 27],

            // Miembros Superiores
            ['snomed_code' => '123942002', 'display' => 'Skin structure of upper limb', 'display_es' => 'Piel del miembro superior', 'category' => 'Miembro Superior', 'sort_order' => 30],
            ['snomed_code' => '244015008', 'display' => 'Skin structure of shoulder', 'display_es' => 'Piel del hombro', 'category' => 'Miembro Superior', 'sort_order' => 31],
            ['snomed_code' => '48075008', 'display' => 'Skin structure of arm', 'display_es' => 'Piel del brazo', 'category' => 'Miembro Superior', 'sort_order' => 32],
            ['snomed_code' => '10547007', 'display' => 'Skin structure of elbow', 'display_es' => 'Piel del codo', 'category' => 'Miembro Superior', 'sort_order' => 33],
            ['snomed_code' => '77753009', 'display' => 'Skin structure of forearm', 'display_es' => 'Piel del antebrazo', 'category' => 'Miembro Superior', 'sort_order' => 34],
            ['snomed_code' => '74670003', 'display' => 'Skin structure of wrist', 'display_es' => 'Piel de la muñeca', 'category' => 'Miembro Superior', 'sort_order' => 35],
            ['snomed_code' => '32696007', 'display' => 'Skin structure of hand', 'display_es' => 'Piel de la mano', 'category' => 'Miembro Superior', 'sort_order' => 36],
            ['snomed_code' => '27268008', 'display' => 'Skin structure of palm', 'display_es' => 'Piel de la palma', 'category' => 'Miembro Superior', 'sort_order' => 37],
            ['snomed_code' => '42564005', 'display' => 'Skin structure of finger', 'display_es' => 'Piel del dedo de la mano', 'category' => 'Miembro Superior', 'sort_order' => 38],

            // Miembros Inferiores
            ['snomed_code' => '123943007', 'display' => 'Skin structure of lower limb', 'display_es' => 'Piel del miembro inferior', 'category' => 'Miembro Inferior', 'sort_order' => 40],
            ['snomed_code' => '78320004', 'display' => 'Skin structure of hip', 'display_es' => 'Piel de la cadera', 'category' => 'Miembro Inferior', 'sort_order' => 41],
            ['snomed_code' => '32937002', 'display' => 'Skin structure of buttock', 'display_es' => 'Piel del glúteo', 'category' => 'Miembro Inferior', 'sort_order' => 42],
            ['snomed_code' => '30021000', 'display' => 'Skin structure of thigh', 'display_es' => 'Piel del muslo', 'category' => 'Miembro Inferior', 'sort_order' => 43],
            ['snomed_code' => '362126009', 'display' => 'Skin structure of knee', 'display_es' => 'Piel de la rodilla', 'category' => 'Miembro Inferior', 'sort_order' => 44],
            ['snomed_code' => '80768000', 'display' => 'Skin structure of leg', 'display_es' => 'Piel de la pierna', 'category' => 'Miembro Inferior', 'sort_order' => 45],
            ['snomed_code' => '67833009', 'display' => 'Skin structure of ankle', 'display_es' => 'Piel del tobillo', 'category' => 'Miembro Inferior', 'sort_order' => 46],
            ['snomed_code' => '56459004', 'display' => 'Skin structure of foot', 'display_es' => 'Piel del pie', 'category' => 'Miembro Inferior', 'sort_order' => 47],
            ['snomed_code' => '76986006', 'display' => 'Skin structure of sole of foot', 'display_es' => 'Piel de la planta del pie', 'category' => 'Miembro Inferior', 'sort_order' => 48],
            ['snomed_code' => '52368001', 'display' => 'Skin structure of toe', 'display_es' => 'Piel del dedo del pie', 'category' => 'Miembro Inferior', 'sort_order' => 49],

            // Regiones Especiales
            ['snomed_code' => '48014002', 'display' => 'Skin structure of groin', 'display_es' => 'Piel de la ingle', 'category' => 'Región Especial', 'sort_order' => 50],
            ['snomed_code' => '87022005', 'display' => 'Skin structure of perineum', 'display_es' => 'Piel del periné', 'category' => 'Región Especial', 'sort_order' => 51],
            ['snomed_code' => '71537002', 'display' => 'Skin structure of genitalia', 'display_es' => 'Piel de los genitales', 'category' => 'Región Especial', 'sort_order' => 52],
        ];

        foreach ($bodySites as $site) {
            SnomedBodySite::updateOrCreate(
                ['snomed_code' => $site['snomed_code']],
                $site
            );
        }

        $this->command->info('SNOMED Body Sites seeded successfully.');
    }
}
