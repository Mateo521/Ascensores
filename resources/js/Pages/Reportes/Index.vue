<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  filtros: Object,
  kpi: Object,
  revisiones: Object, // paginator
})

const desde = ref(props.filtros.desde)
const hasta = ref(props.filtros.hasta)
const estado = ref(props.filtros.estado || '')
const q = ref(props.filtros.q || '')

function buscar() {
  router.get('/reportes', {
    desde: desde.value,
    hasta: hasta.value,
    estado: estado.value || undefined,
    q: q.value || undefined,
  }, { preserveState: true, replace: true })
}

function exportar() {
  const params = new URLSearchParams({
    desde: desde.value,
    hasta: hasta.value,
    estado: estado.value || '',
    q: q.value || '',
    tipo: 'csv',
  }).toString()
  window.open(`/reportes/export?${params}`, '_blank')
}
</script>

<template>
  <Head title="Reportes" />
  <AuthenticatedLayout>
    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      <div>
        <h1 class="text-2xl font-bold">Reportes</h1>
        <p class="text-sm text-gray-600">KPIs y listado filtrado de revisiones</p>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded shadow p-4 grid grid-cols-1 md:grid-cols-6 gap-3">
        <div>
          <label class="text-sm">Desde</label>
          <input v-model="desde" type="date" class="w-full border rounded p-2">
        </div>
        <div>
          <label class="text-sm">Hasta</label>
          <input v-model="hasta" type="date" class="w-full border rounded p-2">
        </div>
        <div>
          <label class="text-sm">Estado</label>
          <select v-model="estado" class="w-full border rounded p-2">
            <option value="">Todos</option>
            <option value="pendiente">Pendiente</option>
            <option value="completada">Completada</option>
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="text-sm">Buscar</label>
          <input v-model="q" type="text" placeholder="Código, edificio, dirección"
                 class="w-full border rounded p-2">
        </div>
        <div class="flex items-end gap-2">
          <button @click="buscar" class="px-3 py-2 rounded bg-blue-600 text-white">Buscar</button>
          <button @click="exportar" class="px-3 py-2 rounded border">Exportar CSV</button>
        </div>
      </div>

      <!-- KPIs -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Ascensores</div>
          <div class="text-3xl font-semibold">{{ kpi.ascensores }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Revisiones</div>
          <div class="text-3xl font-semibold">{{ kpi.revisiones }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Completadas</div>
          <div class="text-3xl font-semibold text-green-600">{{ kpi.completadas }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Pendientes</div>
          <div class="text-3xl font-semibold text-yellow-600">{{ kpi.pendientes }}</div>
        </div>
      </div>

      <!-- Tabla -->
      <div class="bg-white rounded shadow overflow-hidden">
        <div v-if="revisiones.data.length" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Ascensor</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Edificio</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Dirección</th>
                <th class="px-4 py-2"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="r in revisiones.data" :key="r.id" class="hover:bg-gray-50">
                <td class="px-4 py-2 whitespace-nowrap">{{ r.fecha }}</td>
                <td class="px-4 py-2">
                  <span class="px-2 py-1 text-xs rounded-full"
                        :class="{
                          'bg-yellow-100 text-yellow-800': r.estado === 'pendiente',
                          'bg-green-100 text-green-800': r.estado === 'completada'
                        }">
                    {{ r.estado }}
                  </span>
                </td>
                <td class="px-4 py-2">
                  <div class="font-medium">{{ r.ascensor.codigo_interno || r.ascensor.numero_ascensor }}</div>
                  <div class="text-xs text-gray-500">ID: {{ r.ascensor.id }}</div>
                </td>
                <td class="px-4 py-2">{{ r.ascensor.edificio }}</td>
                <td class="px-4 py-2">{{ r.ascensor.direccion }}</td>
                <td class="px-4 py-2 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <Link :href="`/ascensores/${r.ascensor.id}`"
                          class="text-blue-600 hover:text-blue-800 text-sm">Ver ascensor</Link>
                    <a v-if="r.ascensor.qr_slug" :href="`/a/${r.ascensor.qr_slug}`" target="_blank"
                       class="text-sm text-gray-700 hover:text-gray-900">QR</a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="p-8 text-center text-gray-500">
          No hay revisiones en el rango seleccionado.
        </div>

        <!-- Paginación -->
        <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
          <div class="text-sm text-gray-500">
            Mostrando {{ revisiones.from || 0 }}-{{ revisiones.to || 0 }} de {{ revisiones.total || 0 }}
          </div>
          <div class="space-x-2">
            <button :disabled="!revisiones.prev_page_url"
                    @click="router.visit(revisiones.prev_page_url, { preserveState: true })"
                    class="px-3 py-1 rounded border disabled:opacity-50">Anterior</button>
            <button :disabled="!revisiones.next_page_url"
                    @click="router.visit(revisiones.next_page_url, { preserveState: true })"
                    class="px-3 py-1 rounded border disabled:opacity-50">Siguiente</button>
          </div>
        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
