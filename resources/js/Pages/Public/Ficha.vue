<script setup>
defineProps({
  ascensor: Object,
  meses: Object
})

const nombresMes = [
  '', 'Enero','Febrero','Marzo','Abril','Mayo','Junio',
  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
]
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center py-10">
    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-3xl">
      <h1 class="text-2xl font-bold text-center mb-6">
        Calendario anual de mantenimiento
      </h1>

      <div class="mb-4 text-gray-700">
        <p><strong>Código:</strong> {{ ascensor.codigo_interno }}</p>
        <p><strong>Edificio:</strong> {{ ascensor.edificio }}</p>
        <p><strong>Dirección:</strong> {{ ascensor.direccion }}</p>
        <p><strong>Descripción:</strong> {{ ascensor.descripcion }}</p>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div v-for="(checked, m) in meses" :key="m" 
             class="flex items-center justify-between p-3 rounded-xl border bg-gray-50">
          <span class="font-medium">{{ nombresMes[m] }}</span>
          <input type="checkbox" class="size-5 accent-green-600" :checked="checked" disabled>
        </div>
      </div>

      <div class="text-center mt-6 text-sm text-gray-500">
        Última actualización: {{ new Date().toLocaleDateString() }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.size-5 { width: 1.25rem; height: 1.25rem; }
</style>
