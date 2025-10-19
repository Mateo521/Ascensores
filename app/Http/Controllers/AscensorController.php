<?php
// app/Http/Controllers/AscensorController.php

namespace App\Http\Controllers;

use App\Models\Ascensor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class AscensorController extends Controller
{
    public function index()
    {
        $ascensores = Ascensor::with(['ultimaRevision'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Ascensores/Index', [
            'ascensores' => $ascensores,
        ]);
    }

    public function create()
    {
        return Inertia::render('Ascensores/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_interno' => 'required|string|unique:ascensors,codigo_interno',
            'edificio' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'numero_ascensor' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,mantenimiento,inactivo',
        ]);

        // Generar slug único
        $baseSlug = Str::slug($validated['codigo_interno'] . '-' . ($validated['edificio'] ?? 'ascensor'));
        $slug = $baseSlug;
        $counter = 1;

        while (Ascensor::where('qr_slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $validated['qr_slug'] = $slug;

        $ascensor = Ascensor::create($validated);

        return redirect()->route('ascensores.show', $ascensor)
            ->with('success', 'Ascensor creado exitosamente');
    }

 public function show(Ascensor $ascensor)
    {
        // Cargar últimas 10 revisiones
        $ascensor->load(['revisiones' => function ($q) {
            $q->orderBy('fecha', 'desc')->take(10)->with('usuario');
        }]);

        $revisiones = $ascensor->revisiones->map(function ($r) {
            return [
                'id' => $r->id,
                'fecha' => optional($r->fecha)->format('d/m/Y'),
                'estado' => $r->estado,
                'tecnico' => optional($r->usuario)->name,
                'observaciones' => $r->observaciones ? Str::limit($r->observaciones, 120) : null,
            ];
        })->values();

        // Props para la vista
        return Inertia::render('Ascensores/Show', [
            'ascensor' => [
                'id' => $ascensor->id,
                'codigo_interno' => $ascensor->codigo_interno,
                'edificio' => $ascensor->edificio,
                'direccion' => $ascensor->direccion,
                'numero_ascensor' => $ascensor->numero_ascensor,
                'descripcion' => $ascensor->descripcion,
                'estado' => $ascensor->estado,
                'qr_slug' => $ascensor->qr_slug,
            ],
            'revisiones' => $revisiones,
        ]);
    }

    public function edit(Ascensor $ascensor)
    {
        return Inertia::render('Ascensores/Edit', [
            'ascensor' => $ascensor,
        ]);
    }

    public function update(Request $request, Ascensor $ascensor)
    {
        $validated = $request->validate([
            'codigo_interno' => 'required|string|unique:ascensors,codigo_interno,' . $ascensor->id,
            'edificio' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'numero_ascensor' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,mantenimiento,inactivo',
        ]);

        $ascensor->update($validated);

        return redirect()->route('ascensores.show', $ascensor)
            ->with('success', 'Ascensor actualizado exitosamente');
    }

    public function destroy(Ascensor $ascensor)
    {
        $ascensor->delete();

        return redirect()->route('ascensores.index')
            ->with('success', 'Ascensor eliminado exitosamente');
    }
}
