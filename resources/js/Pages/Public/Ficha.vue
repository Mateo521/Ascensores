<!-- resources/js/Pages/Public/Ficha.vue -->
<script setup>
defineProps({
  ascensor: Object,
  meses: Object,
  qr_url: String,
  ultima_actualizacion: String,
  año: Number
})

const nombresMes = [
  '', 'Enero','Febrero','Marzo','Abril','Mayo','Junio',
  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
]
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center py-8 px-4">
    <div class="bg-white shadow-xl rounded-2xl p-6 w-full max-w-5xl">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold">Calendario anual de mantenimiento ({{ año }})</h1>
          <p class="text-sm text-gray-500">Última actualización: {{ ultima_actualizacion || '—' }}</p>
        </div>
        <div class="flex gap-2">
          <a :href="`/a/${ascensor.qr_slug}/pdf`"
             class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
            Descargar PDF
          </a>
        </div>
      </div>

      <!-- Datos + QR -->
      <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_1fr] gap-6 mt-6">
        <div class="border rounded-xl p-4">
          <div class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
              <div class="text-gray-500">Código interno</div>
              <div class="font-semibold text-gray-800">{{ ascensor.codigo_interno }}</div>
            </div>
            <div>
              <div class="text-gray-500">N° ascensor</div>
              <div class="font-semibold text-gray-800">{{ ascensor.numero_ascensor || '—' }}</div>
            </div>
            <div>
              <div class="text-gray-500">Edificio</div>
              <div class="font-semibold text-gray-800">{{ ascensor.edificio }}</div>
            </div>
            <div>
              <div class="text-gray-500">Dirección</div>
              <div class="font-semibold text-gray-800">{{ ascensor.direccion }}</div>
            </div>
          </div>

          <div class="mt-3">
            <div class="text-gray-500 text-sm">Descripción</div>
            <div class="mt-1">{{ ascensor.descripcion || '—' }}</div>
          </div>

          <div class="mt-4">
            <h2 class="font-semibold text-gray-900 mb-2">Calendario anual</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
              <div v-for="m in 12" :key="m"
                   class="flex items-center justify-between p-3 rounded-xl border bg-gray-50">
                <span class="font-medium">{{ nombresMes[m] }}</span>
                <input type="checkbox" class="size-5 accent-green-600" :checked="meses[m]" disabled>
              </div>
            </div>
          </div>
        </div>

        <div class="border rounded-xl p-4 flex flex-col items-center">
          <div class="text-sm text-gray-500 mb-2 text-center">
            Escanee el código para ver esta ficha en línea
          </div>
          <img
            :src="`https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=${encodeURIComponent(qr_url)}`"
            alt="QR público" class="border rounded bg-white p-2" />
          <div class="text-xs text-gray-600 mt-2 break-all text-center">
            {{ qr_url }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.size-5 { width: 1.25rem; height: 1.25rem; }
</style>
