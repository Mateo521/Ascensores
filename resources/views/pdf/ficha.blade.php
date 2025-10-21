{{-- resources/views/pdf/ficha.blade.php --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Ficha ascensor | {{ $ascensor->codigo_interno }}</title>
    <style>
        @page {
            margin: 24mm 18mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111;
            font-size: 12px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .muted {
            color: #555;
        }

        .grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 16px;
            margin-top: 8px;
        }

        .box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
        }

        .label {
            font-size: 11px;
            color: #666;
        }

        .value {
            font-size: 13px;
            font-weight: 600;
        }

        .qr {
            text-align: center;
        }

        .qr img {
            width: 220px;
            height: 220px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .month {
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 8px;
            min-height: 62px;
        }

        .month h4 {
            margin: 0 0 4px 0;
            font-size: 12px;
        }

        .check {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #1a7f37;
            border-radius: 4px;
            vertical-align: middle;
            margin-right: 6px;
        }

        .check.on {
            background: #20c997;
            border-color: #20c997;
            position: relative;
        }

        .check.on::after {
            content: "✓";
            position: absolute;
            top: -4px;
            left: 2px;
            color: white;
            font-size: 14px;
        }

        .legend {
            font-size: 11px;
            color: #666;
            margin-top: 8px;
        }

        .footer {
            margin-top: 16px;
            font-size: 10px;
            color: #666;
            text-align: right;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">Calendario anual de mantenimiento ({{ $año }})</div>
        <div class="muted">Última actualización: {{ $ultima_actualizacion ?? '—' }}</div>
    </div>

    <div class="grid">
        <div class="box">
            <div class="row">
                <div>
                    <div class="label">Código interno</div>
                    <div class="value">{{ $ascensor->codigo_interno }}</div>
                </div>
                <div>
                    <div class="label">N° ascensor</div>
                    <div class="value">{{ $ascensor->numero_ascensor ?? '—' }}</div>
                </div>
                <div>
                    <div class="label">Edificio</div>
                    <div class="value">{{ $ascensor->edificio ?? '—' }}</div>
                </div>
                <div>
                    <div class="label">Dirección</div>
                    <div class="value">{{ $ascensor->direccion ?? '—' }}</div>
                </div>
            </div>

            <div style="margin-top:10px" class="label">Descripción</div>
            <div class="value" style="font-weight: 500;">{{ $ascensor->descripcion ?? '—' }}</div>

            <div style="margin-top:14px;" class="label">Calendario anual</div>
            @php
                $nombres = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            @endphp

            <div class="calendar" style="margin-top:6px;">
                @for ($m = 1; $m <= 12; $m++)
                    <div class="month">
                        <h4>{{ $nombres[$m] }}</h4>
                        @if (is_array($meses[$m]) && ($meses[$m]['firma_tecnico'] ?? null))
                            <img src="{{ $meses[$m]['firma_tecnico'] }}" alt="Firma técnico"
                                style="width: 100%; max-height: 70px; object-fit: contain; border: 1px solid #eee; border-radius: 4px; padding: 2px; background: #fff" />
                            <div style="font-size:10px; color:#666; margin-top:2px;">
                                {{ $meses[$m]['tecnico_nombre'] ?? 'Técnico' }} — {{ $meses[$m]['fecha'] ?? '' }}
                            </div>
                        @else
                            <div>
                                <span
                                    class="check {{ (is_array($meses[$m]) ? ($meses[$m]['checked'] ?? false) : $meses[$m]) ? 'on' : '' }}"></span>
                                <span>{{ (is_array($meses[$m]) ? ($meses[$m]['checked'] ?? false) : $meses[$m]) ? 'Revisión completada' : 'Pendiente' }}</span>
                            </div>
                            <div style="font-size:10px; color:#aaa; margin-top:6px;">Firma técnico</div>
                        @endif
                    </div>
                @endfor
            </div>


            <div class="legend">Marcado ✓ cuando el mes tiene al menos una revisión completada.</div>
        </div>

        <div class="box qr">
            <div class="label">Escanear para ver esta ficha en línea</div>
            <div class="qr-svg">
                {!! $qrSvg !!} {{-- Inserta el SVG directamente --}}
            </div>
            <div class="label" style="margin-top:8px; word-break: break-all;">{{ $qrUrl }}</div>
        </div>
    </div>

    <div class="footer">
        Generado automáticamente — {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>