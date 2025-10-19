<?php
// app/Http/Controllers/ReportesController.php

namespace App\Http\Controllers;

use App\Models\Ascensor;
use App\Models\Revision;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
            'estado' => 'nullable|in:pendiente,completada',
            'q' => 'nullable|string',
        ]);

        $desde = $filtros['desde'] ?? Carbon::now()->startOfMonth()->toDateString();
        $hasta = $filtros['hasta'] ?? Carbon::now()->endOfMonth()->toDateString();

        $query = Revision::with('ascensor')
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->when($filtros['estado'] ?? null, fn($q,$estado) => $q->where('estado',$estado))
            ->when($filtros['q'] ?? null, function ($q, $term) {
                $q->whereHas('ascensor', function ($qq) use ($term) {
                    $qq->where('codigo_interno', 'like', "%{$term}%")
                       ->orWhere('numero_ascensor', 'like', "%{$term}%")
                       ->orWhere('edificio', 'like', "%{$term}%")
                       ->orWhere('direccion', 'like', "%{$term}%");
                });
            });

        // KPIs
        $totalAscensores = Ascensor::count();
        $totalRevisiones = (clone $query)->count();
        $completadas = (clone $query)->where('estado','completada')->count();
        $pendientes = (clone $query)->where('estado','pendiente')->count();

        // Tabla
        $revisiones = $query->orderBy('fecha','desc')->paginate(15)->through(function ($r) {
            return [
                'id' => $r->id,
                'fecha' => optional($r->fecha)->format('d/m/Y'),
                'estado' => $r->estado,
                'ascensor' => [
                    'id' => $r->ascensor?->id,
                    'codigo_interno' => $r->ascensor?->codigo_interno,
                    'numero_ascensor' => $r->ascensor?->numero_ascensor,
                    'edificio' => $r->ascensor?->edificio,
                    'direccion' => $r->ascensor?->direccion,
                    'qr_slug' => $r->ascensor?->qr_slug,
                ]
            ];
        })->appends($filtros);

        return Inertia::render('Reportes/Index', [
            'filtros' => array_merge($filtros, ['desde' => $desde, 'hasta' => $hasta]),
            'kpi' => [
                'ascensores' => $totalAscensores,
                'revisiones' => $totalRevisiones,
                'completadas' => $completadas,
                'pendientes' => $pendientes,
            ],
            'revisiones' => $revisiones,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filtros = $request->validate([
            'desde' => 'required|date',
            'hasta' => 'required|date',
            'estado' => 'nullable|in:pendiente,completada',
            'q' => 'nullable|string',
        ]);

        $query = Revision::with('ascensor')
            ->whereBetween('fecha', [$filtros['desde'], $filtros['hasta']])
            ->when($filtros['estado'] ?? null, fn($q,$estado) => $q->where('estado',$estado))
            ->when($filtros['q'] ?? null, function ($q, $term) {
                $q->whereHas('ascensor', function ($qq) use ($term) {
                    $qq->where('codigo_interno', 'like', "%{$term}%")
                       ->orWhere('numero_ascensor', 'like', "%{$term}%")
                       ->orWhere('edificio', 'like', "%{$term}%")
                       ->orWhere('direccion', 'like', "%{$term}%");
                });
            })
            ->orderBy('fecha','desc')
            ->get();

        $filename = 'reporte_revisiones_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Fecha','Estado','Código','N° ascensor','Edificio','Dirección','URL QR']);

            foreach ($query as $r) {
                $urlQr = $r->ascensor?->qr_slug ? url('/a/'.$r->ascensor->qr_slug) : '';
                fputcsv($out, [
                    optional($r->fecha)->format('Y-m-d'),
                    $r->estado,
                    $r->ascensor?->codigo_interno,
                    $r->ascensor?->numero_ascensor,
                    $r->ascensor?->edificio,
                    $r->ascensor?->direccion,
                    $urlQr,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
