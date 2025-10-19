<?php

namespace App\Http\Controllers;

use App\Models\Ascensor;
use App\Models\Revision;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RevisionesController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'q' => 'nullable|string',
            'estado' => 'nullable|in:pendiente,completada',
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
            'ascensor_id' => 'nullable|exists:ascensors,id',
        ]);

        $revisiones = Revision::with('ascensor')
            ->when($filtros['estado'] ?? null, fn($q, $estado) => $q->where('estado', $estado))
            ->when($filtros['desde'] ?? null, fn($q, $desde) => $q->whereDate('fecha', '>=', $desde))
            ->when($filtros['hasta'] ?? null, fn($q, $hasta) => $q->whereDate('fecha', '<=', $hasta))
            ->when($filtros['ascensor_id'] ?? null, fn($q, $id) => $q->where('ascensor_id', $id))
            ->when($filtros['q'] ?? null, function ($q, $term) {
                $q->whereHas('ascensor', function ($qq) use ($term) {
                    $qq->where('codigo_interno', 'like', "%{$term}%")
                       ->orWhere('numero_ascensor', 'like', "%{$term}%")
                       ->orWhere('edificio', 'like', "%{$term}%")
                       ->orWhere('direccion', 'like', "%{$term}%");
                });
            })
            ->orderBy('fecha', 'desc')
            ->paginate(12)
            ->through(function ($rev) {
                return [
                    'id' => $rev->id,
                    'fecha' => $rev->fecha?->format('d/m/Y'),
                    'estado' => $rev->estado,
                    'ascensor' => [
                        'id' => $rev->ascensor->id,
                        'codigo_interno' => $rev->ascensor->codigo_interno,
                        'numero_ascensor' => $rev->ascensor->numero_ascensor,
                        'edificio' => $rev->ascensor->edificio,
                        'direccion' => $rev->ascensor->direccion,
                    ],
                ];
            })
            ->appends($filtros);

        // Para selector rápido en el panel
        $ascensoresMin = Ascensor::orderBy('codigo_interno')
            ->select('id', 'codigo_interno', 'edificio', 'direccion')
            ->get();

        return Inertia::render('Revisiones/Index', [
            'revisiones' => $revisiones,
            'filtros' => $filtros,
            'ascensores' => $ascensoresMin,
        ]);
    }

 public function create(Request $request)
{
    $ascensor = null;
    $ascensores = [];

    if ($request->filled('ascensor_id')) {
        $ascensor = \App\Models\Ascensor::findOrFail($request->ascensor_id);
    } else {
        $ascensores = \App\Models\Ascensor::orderBy('codigo_interno')
            ->select('id', 'codigo_interno', 'edificio', 'direccion')
            ->get();
    }

    return Inertia::render('Revisiones/Create', [
        'ascensor' => $ascensor,
        'ascensores' => $ascensores,
        'hoy' => now()->toDateString(),
        'hora' => now()->format('H:i'),
    ]);
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'ascensor_id' => 'required|exists:ascensors,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,completada',
            'formulario' => 'nullable|array',
            'observaciones' => 'nullable|string',
        ]);

        Revision::create([
            'ascensor_id' => $data['ascensor_id'],
            'user_id' => auth()->id(),
            'fecha' => Carbon::parse($data['fecha'])->startOfDay(),
            'estado' => $data['estado'],
            'formulario' => $data['formulario'] ?? [],
            'observaciones' => $data['observaciones'] ?? null,
            'sincronizado' => true,
        ]);

        return redirect()->route('revisiones.index')->with('success', 'Revisión guardada');
    }

    // Si usas la sincronización offline por API, deja aquí tu método syncOffline(...)
}