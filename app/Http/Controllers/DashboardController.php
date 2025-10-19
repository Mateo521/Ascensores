<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Ascensor;
use App\Models\Revision;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total' => Ascensor::count(),
            'activos' => Ascensor::where('estado', 'activo')->count(),
            'mantenimiento' => Ascensor::where('estado', 'mantenimiento')->count(),
            'pendientes' => $this->getRevisionesPendientes(),
        ];

        // Ascensores registrados recientemente
        $ascensoresRecientes = Ascensor::latest()
            ->take(5)
            ->get()
            ->map(function ($ascensor) {
                return [
                    'id' => $ascensor->id,
                    'numero_identificador' => $ascensor->numero_ascensor ?? $ascensor->codigo_interno,
                    'edificio' => $ascensor->edificio,
                    'direccion' => $ascensor->direccion,
                    'estado' => $ascensor->estado,
                ];
            });

        // Próximas revisiones (ordenadas por fecha)
        $revisionesProximas = Revision::with(['ascensor', 'usuario'])
            ->where('estado', 'pendiente')
            ->whereDate('fecha', '>=', Carbon::now()->startOfDay())
            ->orderBy('fecha')
            ->take(5)
            ->get()
            ->map(function ($revision) {
                $fechaProgramada = Carbon::parse($revision->fecha);
                $diasRestantes = Carbon::now()->startOfDay()->diffInDays($fechaProgramada, false);
                
                return [
                    'id' => $revision->id,
                    'ascensor' => [
                        'numero_identificador' => $revision->ascensor->numero_ascensor ?? $revision->ascensor->codigo_interno,
                        'edificio' => $revision->ascensor->edificio,
                    ],
                    'fecha_programada' => $fechaProgramada->format('d/m/Y'),
                    'dias_restantes' => (int) $diasRestantes,
                    'estado' => $revision->estado,
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'ascensoresRecientes' => $ascensoresRecientes,
            'revisionesProximas' => $revisionesProximas,
        ]);
    }

    private function getRevisionesPendientes()
    {
        // Contar revisiones pendientes que ya pasaron su fecha
        return Revision::where('estado', 'pendiente')
            ->whereDate('fecha', '<=', Carbon::now())
            ->count();
    }
}
