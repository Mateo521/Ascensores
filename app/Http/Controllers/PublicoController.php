<?php
// app/Http/Controllers/PublicoController.php

namespace App\Http\Controllers;

use App\Models\Ascensor;
use App\Models\Revision;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

use App\Models\Setting;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;
class PublicoController extends Controller
{

    private function normalizeLogoUrl(?string $path): ?string
{
    if (!$path) return null;
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    if (str_starts_with($path, 'storage/')) {
        return asset($path); // /storage/...
    }
    if (str_starts_with($path, 'public/storage/')) {
        return asset(substr($path, 7)); // quita 'public/'
    }
    // Caso típico guardado en disk 'public': logos/archivo.png
    return asset('storage/'.$path);
}
private function logoBase64ForWeb(?string $pathFromSettings = null): ?string
{
    // Si tenés un logo en Settings:
    $path = $pathFromSettings;

    // Fallback a public/storage/logo.png si no hay Settings
    if (!$path) {
        $fullFallback = public_path('storage/logo.png');
        if (file_exists($fullFallback)) {
            return 'data:image/png;base64,'.base64_encode(file_get_contents($fullFallback));
        }
        return null;
    }

    // Resolver a ruta física en public
    if (str_starts_with($path, 'public/')) {
        $publicRelative = substr($path, 7);
        $full = public_path($publicRelative);
    } elseif (str_starts_with($path, 'storage/')) {
        $full = public_path($path);
    } else {
        $full = public_path('storage/'.$path);
    }

    if (!file_exists($full)) return null;

    $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
    $mime = $ext === 'svg' ? 'image/svg+xml' : ('image/'.($ext ?: 'png'));
    return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($full));
}


    private function toBase64OrNull(?string $path): ?string
    {
        if (!$path)
            return null;
        if (!Storage::disk('public')->exists($path))
            return null;
        $bin = Storage::disk('public')->get($path);
        return 'data:image/png;base64,' . base64_encode($bin);
    }

    private function logoBase64(): ?string
    {
        $empresa = Setting::getValue('empresa', []);
        $path = $empresa['logo_path'] ?? null;
        if (!$path || !Storage::disk('public')->exists($path))
            return null;
        $bin = Storage::disk('public')->get($path);
        $mime = 'image/png'; // podrías detectar por extensión
        return 'data:' . $mime . ';base64,' . base64_encode($bin);
    }

    public function show($slug)
    {
        $empresa = Setting::getValue('empresa', []);
        $logoUrl = isset($empresa['logo_path']) ? asset('logos/' . $empresa['logo_path']) : null;

        $ascensor = Ascensor::where('qr_slug', $slug)->firstOrFail();
        $anio = now()->year;

        // Todas las completadas del año (para calendario)
        $revisiones = Revision::with('usuario')
            ->where('ascensor_id', $ascensor->id)
            ->where('estado', 'completada')
            ->whereYear('fecha', $anio)
            ->get();

        $meses = [];
        for ($m = 1; $m <= 12; $m++) {
            // Toma la última revisión completada de ese mes (si hay más de una)
            $revMes = $revisiones
                ->filter(fn($r) => optional($r->fecha)->month == $m)
                ->sortByDesc('fecha')
                ->first();

            $meses[$m] = [
                'checked' => (bool) $revMes,
                'fecha' => $revMes?->fecha?->format('d/m/Y'),
                'tecnico_nombre' => $revMes?->firma_tecnico_nombre ?? ($revMes?->usuario?->name ?? null),
                // La firma puede no existir incluso si la revisión está "completada"
                'firma_tecnico' => $this->toBase64OrNull($revMes?->firma_tecnico_path),
                // Si quieres mostrar firma de cliente:
                // 'firma_cliente' => $this->toBase64OrNull($revMes?->firma_cliente_path),
            ];
        }

        $ultima = Revision::where('ascensor_id', $ascensor->id)
            ->where('estado', 'completada')
            ->orderByDesc('fecha')
            ->first();




        return Inertia::render('Public/Ficha', [

            'ascensor' => [
                'codigo_interno' => $ascensor->codigo_interno,
                'numero_ascensor' => $ascensor->numero_ascensor,
                'edificio' => $ascensor->edificio,
                'direccion' => $ascensor->direccion,
                'descripcion' => $ascensor->descripcion,
                'qr_slug' => $ascensor->qr_slug,
            ],
            'empresa' => [
                'nombre' => $empresa['nombre'] ?? null,
                'cuit' => $empresa['cuit'] ?? null,
                'inicio_actividad' => $empresa['inicio_actividad'] ?? null,
                'telefono' => $empresa['telefono'] ?? null,
                  'logo_url' => asset('storage/logo.png'),
                'ciudad' => 'San Luis',
            ],
            'qr_url' => url('/a/' . $ascensor->qr_slug),
            'meses' => $meses,
            'ultima_actualizacion' => $ultima?->fecha?->format('d/m/Y'),
            'año' => $anio,
            'compact' => request()->boolean('compact'),
        ]);



    }

    public function pdf($slug)
    {
        $ascensor = Ascensor::where('qr_slug', $slug)->firstOrFail();
        $empresa = Setting::getValue('empresa', []);
        $logoBase64 = $this->logoBase64();
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
            'qrUrl' => url('/a/' . $ascensor->qr_slug),


            'empresa' => $empresa,
            'logoBase64' => $logoBase64,
            'ciudad' => 'San Luis',
            'hoy' => now()->locale('es')->timezone(config('app.timezone'))->translatedFormat("d 'de' F 'de' Y"),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('ficha-' . $ascensor->codigo_interno . '.pdf');
    }

}
