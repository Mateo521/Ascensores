<!-- resources/js/Pages/Revisiones/Index.vue -->
<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, computed } from 'vue'
import { useOfflineQueue } from '@/composables/useOfflineQueue.js'

const props = defineProps({
  revisiones: Object,     // paginator
  filtros: Object,
  ascensores: Array,      // para selector rápido
})

const q = ref(props.filtros.q || '')
const estado = ref(props.filtros.estado || '')
const desde = ref(props.filtros.desde || '')
const hasta = ref(props.filtros.hasta || '')
const ascensor_id = ref(props.filtros.ascensor_id || '')

const { syncAll } = useOfflineQueue()
const syncing = ref(false)
const online = computed(() => navigator.onLine)

function buscar() {
  router.get('/revisiones', {
    q: q.value || undefined,
    estado: estado.value || undefined,
    desde: desde.value || undefined,
    hasta: hasta.value || undefined,
    ascensor_id: ascensor_id.value || undefined,
  }, { preserveState: true, replace: true })
}

async function sincronizarPendientes() {
  try {
    syncing.value = true
    const res = await syncAll()
    alert(`Sincronización OK. Registros enviados: ${res.count}`)
    router.reload({ only: ['revisiones'] })
  } catch (e) {
    alert('No se pudo sincronizar: ' + e.message)
  } finally {
    syncing.value = false
  }
}

const crearAscensorId = ref('')
function irACrear() {
  if (!crearAscensorId.value) {
    router.visit('/revisiones/create')
  } else {
    router.visit(`/revisiones/create?ascensor_id=${crearAscensorId.value}`)
  }
}
</script>

<template>
  <Head title="Revisiones" />
  <AuthenticatedLayout>
    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold">Revisiones</h1>
            <p class="text-sm text-gray-600">Listado de revisiones con filtros</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs px-2 py-1 rounded"
                  :class="online ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
              {{ online ? 'Online' : 'Offline' }}
            </span>
            <button @click="sincronizarPendientes"
                    :disabled="syncing"
                    class="px-3 py-2 rounded border bg-gray-50 hover:bg-gray-100 disabled:opacity-50">
              {{ syncing ? 'Sincronizando...' : 'Sincronizar pendientes' }}
            </button>
            <div class="flex items-center gap-2">
              <select v-model="crearAscensorId" class="border rounded p-2">
                <option value="">Selecciona ascensor (opcional)</option>
                <option v-for="a in ascensores" :key="a.id" :value="a.id">
                  {{ a.codigo_interno }} - {{ a.edificio }}
                </option>
              </select>
              <button @click="irACrear"
                      class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                Nueva revisión
              </button>
            </div>
          </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white p-4 rounded shadow space-y-3">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium">Buscar</label>
              <input v-model="q" @keyup.enter="buscar" type="text" placeholder="Código, edificio, dirección"
                     class="w-full border rounded p-2">
            </div>
            <div>
              <label class="block text-sm font-medium">Estado</label>
              <select v-model="estado" class="w-full border rounded p-2">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="completada">Completada</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium">Desde</label>
              <input v-model="desde" type="date" class="w-full border rounded p-2">
            </div>
            <div>
              <label class="block text-sm font-medium">Hasta</label>
              <input v-model="hasta" type="date" class="w-full border rounded p-2">
            </div>
          </div>
          <div class="flex justify-end gap-2">
            <button @click="() => { q = ''; estado = ''; desde = ''; hasta = ''; ascensor_id = ''; buscar() }"
                    class="px-3 py-2 rounded border">
              Limpiar
            </button>
            <button @click="buscar"
                    class="px-3 py-2 rounded bg-blue-600 text-white">
              Buscar
            </button>
          </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded shadow overflow-hidden">
          <div v-if="revisiones.data.length" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ascensor</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Edificio</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dirección</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                  <th class="px-4 py-2"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="r in revisiones.data" :key="r.id" class="hover:bg-gray-50">
                  <td class="px-4 py-2 whitespace-nowrap">{{ r.fecha }}</td>
                  <td class="px-4 py-2">
                    <div class="font-medium">{{ r.ascensor.codigo_interno || r.ascensor.numero_ascensor }}</div>
                    <div class="text-xs text-gray-500">ID: {{ r.ascensor.id }}</div>
                  </td>
                  <td class="px-4 py-2">{{ r.ascensor.edificio }}</td>
                  <td class="px-4 py-2">{{ r.ascensor.direccion }}</td>
                  <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs rounded-full"
                      :class="{
                        'bg-yellow-100 text-yellow-800': r.estado === 'pendiente',
                        'bg-green-100 text-green-800': r.estado === 'completada'
                      }">
                      {{ r.estado }}
                    </span>
                  </td>
                  <td class="px-4 py-2 text-right whitespace-nowrap">
                    <Link :href="`/ascensores/${r.ascensor.id}`"
                          class="text-blue-600 hover:text-blue-800 text-sm">Ver ascensor</Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="p-8 text-center text-gray-500">
            No hay revisiones.
          </div>

          <!-- Paginación simple -->
          <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
            <div class="text-sm text-gray-500">
              Mostrando {{ revisiones.from || 0 }}-{{ revisiones.to || 0 }} de {{ revisiones.total }}
            </div>
            <div class="space-x-2">
              <button :disabled="!revisiones.prev_page_url" @click="router.visit(revisiones.prev_page_url, { preserveState: true })"
                      class="px-3 py-1 rounded border disabled:opacity-50">Anterior</button>
              <button :disabled="!revisiones.next_page_url" @click="router.visit(revisiones.next_page_url, { preserveState: true })"
                      class="px-3 py-1 rounded border disabled:opacity-50">Siguiente</button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
