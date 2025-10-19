<?php
// app/Http/Controllers/AscensorController.php

namespace App\Http\Controllers;

use App\Models\Ascensor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AscensorController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'q' => 'nullable|string',
            'estado' => 'nullable|in:activo,mantenimiento,inactivo',
            'per_page' => 'nullable|integer|min:5|max:100',
        ]);

        $perPage = $filtros['per_page'] ?? 10;

        $ascensores = Ascensor::query()
            ->when($filtros['estado'] ?? null, fn($q, $estado) => $q->where('estado', $estado))
            ->when($filtros['q'] ?? null, function ($q, $term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('codigo_interno', 'like', "%{$term}%")
                       ->orWhere('numero_ascensor', 'like', "%{$term}%")
                       ->orWhere('edificio', 'like', "%{$term}%")
                       ->orWhere('direccion', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($filtros);

        return Inertia::render('Ascensores/Index', [
            'ascensores' => $ascensores,
            'filtros' => $filtros,
        ]);
    }

    public function create()
    {
        return Inertia::render('Ascensores/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_interno'   => 'required|string|unique:ascensors,codigo_interno',
            'edificio'         => 'nullable|string|max:255',
            'direccion'        => 'nullable|string|max:255',
            'numero_ascensor'  => 'nullable|string|max:255',
            'descripcion'      => 'nullable|string',
            'estado'           => 'required|in:activo,mantenimiento,inactivo',
        ]);

        // Generar qr_slug si no viene
        $baseSlug = Str::slug($validated['codigo_interno'].'-'.($validated['edificio'] ?? 'ascensor'));
        $slug = $baseSlug; $i = 1;
        while (Ascensor::where('qr_slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }
        $validated['qr_slug'] = $slug;

        // Crear
        $ascensor = Ascensor::query()->create($validated);

        return redirect()
            ->route('ascensores.show', $ascensor)
            ->with('success', 'Ascensor creado exitosamente');
    }

    public function show(Ascensor $ascensor)
    {
        // Ya hicimos este método en mensajes anteriores.
        // Asegúrate que envía 'ascensor' con qr_public_url y 'revisiones'.
        // Si no lo tienes, solicita que lo vuelva a incluir.
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
                'qr_public_url' => url('/a/'.$ascensor->qr_slug),
            ],
            'revisiones' => [], // opcional si aún no incluyes revisiones aquí
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
            'codigo_interno'   => 'required|string|unique:ascensors,codigo_interno,' . $ascensor->id,
            'edificio'         => 'nullable|string|max:255',
            'direccion'        => 'nullable|string|max:255',
            'numero_ascensor'  => 'nullable|string|max:255',
            'descripcion'      => 'nullable|string',
            'estado'           => 'required|in:activo,mantenimiento,inactivo',
        ]);

        $ascensor->update($validated);

        return redirect()
            ->route('ascensores.show', $ascensor)
            ->with('success', 'Ascensor actualizado exitosamente');
    }

    public function destroy(Ascensor $ascensor)
    {
        $ascensor->delete();

        return redirect()
            ->route('ascensores.index')
            ->with('success', 'Ascensor eliminado');
    }
}
