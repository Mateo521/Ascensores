<?php
// database/seeders/DashboardSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ascensor;
use App\Models\Revision;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardSeeder extends Seeder
{
    public function run()
    {
        // Crear un usuario técnico si no existe
        $tecnico = User::firstOrCreate(
            ['email' => 'tecnico@test.com'],
            [
                'name' => 'Técnico de Prueba',
                'password' => bcrypt('password'),
                'rol' => 'tecnico',
            ]
        );

        $this->command->info('✅ Usuario técnico creado: tecnico@test.com / password');

        // Crear algunos ascensores de prueba
        $ascensores = [
            [
                'codigo_interno' => 'TC-001',
                'edificio' => 'Torre Central',
                'direccion' => 'Av. Illia 123, San Luis',
                'numero_ascensor' => 'ASC-001',
                'descripcion' => 'Ascensor principal - 8 pisos',
                'estado' => 'activo',
                'qr_slug' => Str::slug('tc-001-torre-central'),
            ],
            [
                'codigo_interno' => 'EM-001',
                'edificio' => 'Edificio Municipal',
                'direccion' => 'Pringles 456, San Luis',
                'numero_ascensor' => 'ASC-002',
                'descripcion' => 'Ascensor de carga - 5 pisos',
                'estado' => 'activo',
                'qr_slug' => Str::slug('em-001-edificio-municipal'),
            ],
            [
                'codigo_interno' => 'HC-001',
                'edificio' => 'Hospital Central',
                'direccion' => 'Av. Lafinur 789, San Luis',
                'numero_ascensor' => 'ASC-003',
                'descripcion' => 'Ascensor de emergencia - 10 pisos',
                'estado' => 'mantenimiento',
                'qr_slug' => Str::slug('hc-001-hospital-central'),
            ],
            [
                'codigo_interno' => 'CC-001',
                'edificio' => 'Centro Comercial',
                'direccion' => 'San Martín 321, San Luis',
                'numero_ascensor' => 'ASC-004',
                'descripcion' => 'Ascensor público - 4 pisos',
                'estado' => 'activo',
                'qr_slug' => Str::slug('cc-001-centro-comercial'),
            ],
            [
                'codigo_interno' => 'RP-001',
                'edificio' => 'Residencial Las Palmas',
                'direccion' => 'Junín 654, San Luis',
                'numero_ascensor' => 'ASC-005',
                'descripcion' => 'Ascensor residencial - 6 pisos',
                'estado' => 'activo',
                'qr_slug' => Str::slug('rp-001-residencial-palmas'),
            ],
        ];

        foreach ($ascensores as $ascensorData) {
            $ascensor = Ascensor::create($ascensorData);

            // Crear revisiones mensuales para los últimos 3 meses (completadas)
            for ($i = 3; $i >= 1; $i--) {
                $fecha = Carbon::now()->subMonths($i)->startOfMonth();
                
                Revision::create([
                    'ascensor_id' => $ascensor->id,
                    'user_id' => $tecnico->id,
                    'fecha' => $fecha,
                    'estado' => 'completada',
                    'formulario' => [
                        'motor' => 'ok',
                        'cables' => 'ok',
                        'puertas' => 'ok',
                        'botonera' => 'ok',
                        'iluminacion' => 'ok',
                    ],
                    'observaciones' => 'Revisión mensual completada sin novedades',
                    'sincronizado' => true,
                ]);
            }

            // Crear revisión del mes actual (pendiente)
            Revision::create([
                'ascensor_id' => $ascensor->id,
                'user_id' => null,
                'fecha' => Carbon::now()->startOfMonth(),
                'estado' => 'pendiente',
                'formulario' => null,
                'observaciones' => null,
                'sincronizado' => false,
            ]);

            // Crear revisiones futuras (próximos 3 meses)
            for ($i = 1; $i <= 3; $i++) {
                $fecha = Carbon::now()->addMonths($i)->startOfMonth();
                
                Revision::create([
                    'ascensor_id' => $ascensor->id,
                    'user_id' => null,
                    'fecha' => $fecha,
                    'estado' => 'pendiente',
                    'formulario' => null,
                    'observaciones' => null,
                    'sincronizado' => false,
                ]);
            }

            // Crear 1-2 revisiones vencidas (mes pasado sin completar)
            if (rand(0, 1)) {
                Revision::create([
                    'ascensor_id' => $ascensor->id,
                    'user_id' => null,
                    'fecha' => Carbon::now()->subMonth()->startOfMonth(),
                    'estado' => 'pendiente',
                    'formulario' => null,
                    'observaciones' => null,
                    'sincronizado' => false,
                ]);
            }
        }

        $this->command->info('✅ Se crearon ' . count($ascensores) . ' ascensores con sus revisiones');
    }
}
