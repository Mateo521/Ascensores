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
            'nombre' => 'Ascensores SL',
            'direccion' => 'San Luis, Argentina',
            'telefono' => '+54 266 xxx xxxx',
            'email' => 'contacto@empresa.com',
        ]);

        Setting::setValue('app', [
            'offline_enabled' => true,
            'checklist_categories' => ['mecanico', 'electrico', 'seguridad', 'cabina', 'puertas'],
            'pdf_footer' => 'Revisiones de mantenimiento - Ascensores SL',
        ]);
    }
}
