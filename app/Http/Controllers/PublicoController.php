<?php
// app/Http/Controllers/PublicoController.php

namespace App\Http\Controllers;

use App\Models\Ascensor;
use App\Models\Revision;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
class PublicoController extends Controller
{
    public function show($slug)
    {
        $ascensor = Ascensor::where('qr_slug', $slug)->firstOrFail();

        $añoActual = Carbon::now()->year;
        $revisiones = Revision::where('ascensor_id', $ascensor->id)
            ->where('estado', 'completada')
            ->whereYear('fecha', $añoActual)
            ->get();

        $meses = [];
        for ($mes = 1; $mes <= 12; $mes++) {
            $revision = $revisiones->first(function ($r) use ($mes) {
                return Carbon::parse($r->fecha)->month == $mes;
            });
            $meses[$mes] = $revision ? true : false;
        }

        $ultima = Revision::where('ascensor_id', $ascensor->id)
            ->where('estado', 'completada')
            ->orderByDesc('fecha')
            ->first();

        return Inertia::render('Public/Ficha', [
            'ascensor' => [
                'codigo_interno' => $ascensor->codigo_interno,
                'edificio' => $ascensor->edificio,
                'direccion' => $ascensor->direccion,
                'descripcion' => $ascensor->descripcion,
                'numero_ascensor' => $ascensor->numero_ascensor,
                'qr_slug' => $ascensor->qr_slug, // ← importante para Vue
            ],
            'qr_url' => url('/a/' . $ascensor->qr_slug), // URL absoluta para QR
            'meses' => $meses,
            'ultima_actualizacion' => optional($ultima?->fecha)->format('d/m/Y'),
            'año' => $añoActual,
        ]);
    }

    public function pdf($slug)
{
    $ascensor = Ascensor::where('qr_slug', $slug)->firstOrFail();

    $añoActual = now()->year;
    $revisiones = Revision::where('ascensor_id', $ascensor->id)
        ->where('estado', 'completada')
        ->whereYear('fecha', $añoActual)
        ->get();

    $meses = [];
    for ($m = 1; $m <= 12; $m++) {
        $meses[$m] = $revisiones->first(fn($r) => $r->fecha->month == $m) ? true : false;
    }

    $ultima = Revision::where('ascensor_id', $ascensor->id)
        ->where('estado', 'completada')
        ->orderByDesc('fecha')
        ->first();

    // Generar QR SVG (sin GD)
    $qr = Builder::create()
        ->writer(new SvgWriter())
        ->data(url('/a/' . $ascensor->qr_slug))
        ->size(420)
        ->margin(0)
        ->build();

    $qrSvg = $qr->getString(); // markup SVG

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ficha', [
        'ascensor' => $ascensor,
        'meses' => $meses,
        'año' => $añoActual,
        'ultima_actualizacion' => optional($ultima?->fecha)->format('d/m/Y'),
        'qrSvg' => $qrSvg,               // ← pasamos SVG embebible
        'qrUrl' => url('/a/'.$ascensor->qr_slug),
    ])->setPaper('a4', 'portrait');

    return $pdf->download('ficha-'.$ascensor->codigo_interno.'.pdf');
}

}
