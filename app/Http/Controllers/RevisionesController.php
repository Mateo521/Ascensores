<?php

namespace App\Http\Controllers;

use App\Models\Ascensor;
use App\Models\Revision;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
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


    private function guardarFirmaBase64(?string $base64, string $prefijo): ?string
{
    if (!$base64) return null;
    $bin = base64_decode(preg_replace('#^data:image/\w+;base64,#', '', $base64));
    $name = $prefijo.'_'.uniqid().'.png';
    Storage::disk('public')->put('firmas/'.$name, $bin);
    return 'firmas/'.$name;
}


    private function generarHashVerificacion(int $revisionId): string
    {
        return hash_hmac('sha256', $revisionId . '|' . now()->timestamp, config('app.key'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'ascensor_id' => 'required|exists:ascensors,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,completada',
            'formulario' => 'nullable|array',
            'observaciones' => 'nullable|string',
            'firma_tecnico_base64' => 'required_if:estado,completada|nullable|string',
            'firma_tecnico_nombre' => 'required_if:estado,completada|nullable|string|max:120',
            'firma_cliente_base64' => 'nullable|string',
            'firma_cliente_nombre' => 'nullable|string|max:120',
        ], [
            'firma_tecnico_base64.required_if' => 'La firma del técnico es obligatoria para completar la revisión.',
            'firma_tecnico_nombre.required_if' => 'El nombre del técnico es obligatorio para completar la revisión.',
        ]);
        DB::beginTransaction();

        try {
            $rev = Revision::create([
                'ascensor_id' => $data['ascensor_id'],
                'user_id' => auth()->id(),
                'fecha' => $data['fecha'], // 'Y-m-d'
                'estado' => $data['estado'],
                'formulario' => $data['formulario'] ?? [],
                'observaciones' => $data['observaciones'] ?? null,
                'sincronizado' => true,
            ]);

            $tecPath = $this->guardarFirmaBase64($data['firma_tecnico_base64'] ?? null, 'tec_' . $rev->id);
            $cliPath = $this->guardarFirmaBase64($data['firma_cliente_base64'] ?? null, 'cli_' . $rev->id);

            $rev->firma_tecnico_path = $tecPath;
            $rev->firma_cliente_path = $cliPath;
            $rev->firma_tecnico_nombre = $data['firma_tecnico_nombre'] ?? null;
            $rev->firma_cliente_nombre = $data['firma_cliente_nombre'] ?? null;

            if ($tecPath) {
                $rev->firmado_at = now();
                $rev->verificacion_hash = $this->generarHashVerificacion($rev->id);
            }

            $rev->save();

            DB::commit();
            return redirect()->route('revisiones.index')->with('success', 'Revisión guardada');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error al guardar revisión', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'No se pudo guardar la revisión. Revisa los logs.');
        }
    }


    public function show(Revision $revision)
{
    $revision->load('ascensor', 'usuario');

    return Inertia::render('Revisiones/Show', [
        'revision' => [
            'id' => $revision->id,

            
            'fecha' => $revision->fecha?->format('Y-m-d'),

            'estado' => $revision->estado,
            'observaciones' => $revision->observaciones,
            'formulario' => $revision->formulario ?? [],

            
            'firmado_at' => $revision->firmado_at?->timezone(config('app.timezone'))?->format('d/m/Y H:i'),

            'firma_tecnico_nombre' => $revision->firma_tecnico_nombre,
            'firma_cliente_nombre' => $revision->firma_cliente_nombre,
            'firma_tecnico_url' => $revision->firma_tecnico_path ? asset('storage/'.$revision->firma_tecnico_path) : null,
            'firma_cliente_url' => $revision->firma_cliente_path ? asset('storage/'.$revision->firma_cliente_path) : null,
            'verificacion_hash' => $revision->verificacion_hash,

            'ascensor' => [
                'id' => $revision->ascensor->id,
                'codigo_interno' => $revision->ascensor->codigo_interno,
                'numero_ascensor' => $revision->ascensor->numero_ascensor,
                'edificio' => $revision->ascensor->edificio,
                'direccion' => $revision->ascensor->direccion,
                'qr_slug' => $revision->ascensor->qr_slug,
            ],
            'tecnico' => [
                'id' => $revision->usuario?->id,
                'name' => $revision->usuario?->name,
                'email' => $revision->usuario?->email,
            ],
        ],
    ]);
}


    public function update(Request $request, Revision $revision)
    {
        $data = $request->validate([
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,completada',
            'formulario' => 'nullable|array',
            'observaciones' => 'nullable|string',
            'firma_tecnico_base64' => 'nullable|string',
            'firma_cliente_base64' => 'nullable|string',
            'firma_tecnico_nombre' => 'nullable|string|max:120',
            'firma_cliente_nombre' => 'nullable|string|max:120',
        ]);

        DB::beginTransaction();
        try {
            $revision->fecha = $data['fecha'];
            $revision->estado = $data['estado'];
            $revision->formulario = $data['formulario'] ?? $revision->formulario;
            $revision->observaciones = $data['observaciones'] ?? $revision->observaciones;
            $revision->sincronizado = true;

            // Guardar firmas si llegan
            if (!empty($data['firma_tecnico_base64'])) {
                $revision->firma_tecnico_path = $this->guardarFirmaBase64($data['firma_tecnico_base64'], 'tec_' . $revision->id);
                $revision->firma_tecnico_nombre = $data['firma_tecnico_nombre'] ?? $revision->firma_tecnico_nombre;
                $revision->firmado_at = now();
                if (!$revision->verificacion_hash) {
                    $revision->verificacion_hash = $this->generarHashVerificacion($revision->id);
                }
            }
            if (!empty($data['firma_cliente_base64'])) {
                $revision->firma_cliente_path = $this->guardarFirmaBase64($data['firma_cliente_base64'], 'cli_' . $revision->id);
                $revision->firma_cliente_nombre = $data['firma_cliente_nombre'] ?? $revision->firma_cliente_nombre;
            }

            $revision->save();
            DB::commit();

            return back()->with('success', 'Revisión actualizada');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error al actualizar revisión', ['error' => $e->getMessage()]);
            return back()->with('error', 'No se pudo actualizar la revisión.');
        }
    }

    // Si usas la sincronización offline por API, deja aquí tu método syncOffline(...)
}