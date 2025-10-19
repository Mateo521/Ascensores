<?php
// app/Http/Controllers/PublicoController.php

namespace App\Http\Controllers;

use App\Models\Ascensor;
use App\Models\Revision;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class PublicoController extends Controller
{
    public function show($slug)
    {
        $ascensor = Ascensor::where('qr_slug', $slug)->firstOrFail();

        // Obtener revisiones completadas del año actual
        $añoActual = Carbon::now()->year;
        
        $revisiones = Revision::where('ascensor_id', $ascensor->id)
            ->where('estado', 'completada')
            ->whereYear('fecha', $añoActual)
            ->get();

        // Crear array de meses (1-12) con estado de revisión
        $meses = [];
        for ($mes = 1; $mes <= 12; $mes++) {
            $revision = $revisiones->first(function ($r) use ($mes) {
                return Carbon::parse($r->fecha)->month == $mes;
            });
            $meses[$mes] = $revision ? true : false;
        }

        return Inertia::render('Public/Ficha', [
            'ascensor' => [
                'codigo_interno' => $ascensor->codigo_interno,
                'edificio' => $ascensor->edificio,
                'direccion' => $ascensor->direccion,
                'descripcion' => $ascensor->descripcion,
                'numero_ascensor' => $ascensor->numero_ascensor,
            ],
            'meses' => $meses,
        ]);
    }
}
