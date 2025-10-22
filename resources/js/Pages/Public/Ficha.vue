<script setup>
// Desestructura las props para poder usarlas en el script
const { ascensor, meses, qr_url, ultima_actualizacion, año, compact, empresa } = defineProps({
  ascensor: Object,
  meses: Object,
  qr_url: String,
  ultima_actualizacion: String,
  año: Number,
  compact: Boolean,
  empresa: Object, // ← importante
})
const nombresMes = [
  '', 'Enero','Febrero','Marzo','Abril','Mayo','Junio',
  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
]

// Helpers null-safe (soportan también formato boolean legado)
function isChecked(m) {
  const v = meses?.[m]
  return typeof v === 'boolean' ? v : !!v?.checked
}
function firmaSrc(m) {
  const v = meses?.[m]
  return typeof v === 'object' && v ? (v.firma_tecnico || null) : null
}
function firmaInfo(m) {
  const v = meses?.[m]
  if (typeof v !== 'object' || !v) return { nombre: null, fecha: null }
  return { nombre: v.tecnico_nombre || null, fecha: v.fecha || null }
}
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center py-8 px-4">
    <div class="bg-white shadow-xl rounded-2xl p-6 w-full max-w-5xl">





      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
      
      
      
      
           <!-- Encabezado con logo + cuadro info -->
       <div class="flex items-start justify-between gap-4 mb-4">
    <div class="flex items-center gap-3">
      <img v-if="empresa?.logo_url" :src="empresa.logo_url" alt="Logo" class="h-12 object-contain">
      <div class="text-lg font-semibold">{{ empresa?.nombre || '' }}</div>
    </div>
    <div class="border rounded-lg p-3 text-sm leading-tight">
      <div>San Luis, {{ new Date().toLocaleDateString('es-AR', { day:'2-digit', month:'long', year:'numeric' }) }}</div>
      <div v-if="empresa?.inicio_actividad">Inicio de actividad: {{ new Date(empresa.inicio_actividad).toLocaleDateString('es-AR') }}</div>
      <div v-if="empresa?.cuit">CUIT Nº {{ empresa.cuit }}</div>
    </div>
  </div>

      <!-- Botón PDF -->
      <div class="flex justify-end mb-2">
        <a :href="`/a/${ascensor.qr_slug}/pdf`" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
          Descargar PDF
        </a>
      </div>



      </div>

      <!-- Datos + QR (oculto en modo compacto) -->
      <div v-if="!compact" class="grid grid-cols-1 lg:grid-cols-[1.2fr_1fr] gap-6 mt-6">
        <div class="border rounded-xl p-4">
          <div class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
              <div class="text-gray-500">Código interno</div>
              <div class="font-semibold text-gray-800">{{ ascensor.codigo_interno || '—' }}</div>
            </div>
            <div>
              <div class="text-gray-500">N° ascensor</div>
              <div class="font-semibold text-gray-800">{{ ascensor.numero_ascensor || '—' }}</div>
            </div>
            <div>
              <div class="text-gray-500">Edificio</div>
              <div class="font-semibold text-gray-800">{{ ascensor.edificio || '—' }}</div>
            </div>
            <div>
              <div class="text-gray-500">Dirección</div>
              <div class="font-semibold text-gray-800">{{ ascensor.direccion || '—' }}</div>
            </div>
          </div>

          <div class="mt-3">
            <div class="text-gray-500 text-sm">Descripción</div>
            <div class="mt-1">{{ ascensor.descripcion || '—' }}</div>
          </div>
        </div>

        <div class="border rounded-xl p-4 flex flex-col items-center">
          <div class="text-sm text-gray-500 mb-2 text-center">
            Escanee para ver esta ficha en línea
          </div>
          <img
            :src="`https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=${encodeURIComponent(qr_url + '?compact=1')}`"
            alt="QR público" class="border rounded bg-white p-2" />
          <div class="text-xs text-gray-600 mt-2 break-all text-center">
            {{ qr_url }}?compact=1
          </div>
        </div>
      </div>

      <!-- Calendario con firma -->
      <div class="mt-6">
        <h2 class="font-semibold text-gray-900 mb-2">Calendario anual</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
          <div v-for="m in 12" :key="m" class="p-3 rounded-xl border bg-gray-50">
            <div class="flex items-center justify-between">
              <span class="font-medium">{{ nombresMes[m] }}</span>
              <input type="checkbox" class="size-5 accent-green-600" :checked="isChecked(m)" disabled>
            </div>

            <template v-if="firmaSrc(m)">
              <img :src="firmaSrc(m)" alt="Firma técnico"
                   class="w-full max-h-24 object-contain border bg-white rounded p-1 mt-2">
              <div class="text-xs text-gray-500 mt-1">
                {{ firmaInfo(m).nombre || 'Técnico' }} — {{ firmaInfo(m).fecha || '' }}
              </div>
            </template>
            <template v-else>
              <div class="text-xs text-gray-400 mt-2 italic">
                Firma técnico
              </div>
            </template>
          </div>
        </div>

        <div v-if="!Object.values(meses || {}).some(v => typeof v === 'boolean' ? v : v?.checked)" class="mt-3">
          <p class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded p-2">
            No hay revisiones completadas registradas para {{ año }}.
          </p>
        </div>
      </div>

      <!-- Ver ficha completa desde modo compacto -->
      <div v-if="compact" class="mt-6 text-center">
        <a :href="qr_url" class="text-blue-600 hover:text-blue-800 text-sm">Ver ficha completa</a>
      </div>
        <div class="mt-6 text-center text-sm font-semibold" v-if="empresa?.telefono">
    EMERGENCIAS TELÉFONO {{ empresa.telefono }}
  </div>
    </div>
  </div>
</template>

<style scoped>
.size-5 { width: 1.25rem; height: 1.25rem; }
</style>
