<?php
// database/seeders/SettingsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::setValue('empresa', [
            'nombre' => 'Ascensores Nuevo Cuyo',
            'cuit' => '23-17123468-9',
            'inicio_actividad' => '1997-10-10',
            'direccion' => 'San Luis, Argentina',
            'telefono' => '2664555572',
            'logo_path' => '/storage/logo.png',
            'email' => 'contacto@empresa.com',
        ]);

        Setting::setValue('app', [
            'offline_enabled' => true,
            'checklist_categories' => ['mecanico', 'electrico', 'seguridad', 'cabina', 'puertas'],
            'pdf_footer' => 'Revisiones de mantenimiento - Ascensores Nuevo Cuyo',
        ]);
    }
}
