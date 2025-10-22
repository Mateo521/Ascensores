<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Ficha ascensor | {{ $ascensor->codigo_interno }}</title>
  <style>
    @page { margin: 18mm 16mm; }
    body { font-family: DejaVu Sans, sans-serif; color: #111; font-size: 12px; }
    .row { display: flex; justify-content: space-between; align-items: center; }
    .muted { color: #555; }
    .box { border: 1px solid #ddd; border-radius: 8px; padding: 12px; }
    .label { font-size: 11px; color: #666; }
    .value { font-size: 13px; font-weight: 600; }
    .qr svg { width: 220px; height: 220px; }
    .calendar { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .month { border: 1px solid #e5e5e5; border-radius: 8px; padding: 8px; min-height: 62px; }
    .month h4 { margin: 0 0 4px 0; font-size: 12px; }
    .check { display: inline-block; width: 14px; height: 14px; border: 2px solid #1a7f37; border-radius: 4px; vertical-align: middle; margin-right: 6px; }
    .check.on { background: #20c997; border-color: #20c997; position: relative; }
    .check.on::after { content: "✓"; position: absolute; top: -4px; left: 2px; color: white; font-size: 14px; }
    .footer { margin-top: 14px; font-size: 11px; color: #333; text-align: center; font-weight: 600; }
    .logo { height: 42px; }
    .grid2 { display: grid; grid-template-columns: 1.2fr 1fr; gap: 14px; margin-top: 8px; }
    .grid4 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  </style>
</head>
<body>

  <!-- Encabezado con logo + cuadro informativo -->
 <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
  <div style="display:flex;gap:10px;align-items:center;">
    @if(!empty($logoBase64))
      <img src="{{ $logoBase64 }}" alt="Logo" style="height:42px;">
    @endif
    <div style="font-size:16px;font-weight:700;">
      {{ $empresa['nombre'] ?? '' }}
    </div>
  </div>

  <div style="border:1px solid #ddd;border-radius:8px;padding:8px;font-size:11px;line-height:1.3;">
    <div>San Luis, {{ $hoy }}</div>
    @if(!empty($empresa['inicio_actividad']))
      <div>Inicio de actividad: {{ \Carbon\Carbon::parse($empresa['inicio_actividad'])->format('d/m/Y') }}</div>
    @endif
    @if(!empty($empresa['cuit']))
      <div>CUIT Nº {{ $empresa['cuit'] }}</div>
    @endif
  </div>
</div>

  <div class="row" style="margin-bottom: 6px;">
    <div class="title" style="font-size: 18px; font-weight: 700;">
      Calendario anual de mantenimiento ({{ $año }})
    </div>
    <div class="muted">Última actualización: {{ $ultima_actualizacion ?? '—' }}</div>
  </div>

  <div class="grid2">
    <div class="box">
      <div class="grid4">
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
      <div class="calendar" style="margin-top:6px;">
        @php
          $nombres = ['', 'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        @endphp
        @for ($m = 1; $m <= 12; $m++)
          <div class="month">
            <h4>{{ $nombres[$m] }}</h4>
            @php
              $mes = $meses[$m] ?? null;
              $checked = is_array($mes) ? ($mes['checked'] ?? false) : (bool)$mes;
              $firma = is_array($mes) ? ($mes['firma_tecnico'] ?? null) : null;
              $nombre = is_array($mes) ? ($mes['tecnico_nombre'] ?? null) : null;
              $fecha = is_array($mes) ? ($mes['fecha'] ?? null) : null;
            @endphp

            @if ($firma)
              <img src="{{ $firma }}" alt="Firma técnico"
                   style="width: 100%; max-height: 70px; object-fit: contain; border: 1px solid #eee; border-radius: 4px; padding: 2px; background: #fff" />
              <div style="font-size:10px; color:#666; margin-top:2px;">
                {{ $nombre ?? 'Técnico' }} — {{ $fecha ?? '' }}
              </div>
            @else
              <div>
                <span class="check {{ $checked ? 'on' : '' }}"></span>
                <span>{{ $checked ? 'Revisión completada' : 'Pendiente' }}</span>
              </div>
              <div style="font-size:10px; color:#aaa; margin-top:6px;">Firma técnico</div>
            @endif
          </div>
        @endfor
      </div>
    </div>

    <div class="box qr" style="text-align:center;">
      <div class="label">Escanear para ver esta ficha en línea (vista móvil)</div>
      {!! $qrSvg !!} {{-- SVG del QR --}}
      <div class="label" style="margin-top:8px; word-break: break-all;">{{ $qrUrl }}?compact=1</div>
    </div>
  </div>

  <div class="footer">
 @if(!empty($empresa['telefono']))
  <div style="margin-top:14px;text-align:center;font-size:11px;font-weight:600;">
    EMERGENCIAS TELÉFONO {{ $empresa['telefono'] }}
  </div>
@endif
  </div>
</body>
</html>
