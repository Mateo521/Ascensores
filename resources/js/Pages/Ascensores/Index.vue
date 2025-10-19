<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  ascensores: Object, // paginator
  filtros: Object,
})

const q = ref(props.filtros?.q || '')
const estado = ref(props.filtros?.estado || '')
const perPage = ref(props.filtros?.per_page || 10)

function buscar() {
  router.get('/ascensores', {
    q: q.value || undefined,
    estado: estado.value || undefined,
    per_page: perPage.value || undefined,
  }, { preserveState: true, replace: true })
}

function limpiar() {
  q.value = ''
  estado.value = ''
  perPage.value = 10
  buscar()
}

function copiar(texto) {
  navigator.clipboard.writeText(texto).then(
    () => alert('Copiado al portapapeles'),
    () => alert('No se pudo copiar')
  )
}

function eliminar(id) {
  if (!confirm('¿Eliminar este ascensor?')) return
  router.delete(`/ascensores/${id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Ascensores" />
  <AuthenticatedLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold">Ascensores</h1>
          <p class="text-sm text-gray-600">Listado general de ascensores</p>
        </div>
        <Link href="/ascensores/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          Nuevo ascensor
        </Link>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded shadow p-4">
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
              <option value="activo">Activo</option>
              <option value="mantenimiento">Mantenimiento</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium">Por página</label>
            <select v-model.number="perPage" class="w-full border rounded p-2">
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>
          </div>
          <div class="flex items-end gap-2">
            <button @click="limpiar" class="px-3 py-2 rounded border">Limpiar</button>
            <button @click="buscar" class="px-3 py-2 rounded bg-blue-600 text-white">Buscar</button>
          </div>
        </div>
      </div>

      <!-- Tabla -->
      <div class="bg-white rounded shadow overflow-hidden">
        <div v-if="ascensores?.data?.length" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Código</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">N° ascensor</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Edificio</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Dirección</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">QR</th>
                <th class="px-4 py-2"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="a in ascensores.data" :key="a.id" class="hover:bg-gray-50">
                <td class="px-4 py-2">{{ a.codigo_interno }}</td>
                <td class="px-4 py-2">{{ a.numero_ascensor || '—' }}</td>
                <td class="px-4 py-2">{{ a.edificio || '—' }}</td>
                <td class="px-4 py-2">{{ a.direccion || '—' }}</td>
                <td class="px-4 py-2">
                  <span class="px-2 py-1 text-xs rounded-full"
                        :class="{
                          'bg-green-100 text-green-800': a.estado === 'activo',
                          'bg-red-100 text-red-800': a.estado === 'mantenimiento',
                          'bg-gray-100 text-gray-800': a.estado === 'inactivo'
                        }">
                    {{ a.estado }}
                  </span>
                </td>
                <td class="px-4 py-2">
                  <div class="flex items-center gap-2">
                    <a v-if="a.qr_slug" :href="`/a/${a.qr_slug}`" target="_blank"
                       class="text-blue-600 hover:text-blue-800 text-sm">Ver QR</a>
                    <button v-if="a.qr_slug" @click="copiar(`${window.location.origin}/a/${a.qr_slug}`)"
                            class="text-xs px-2 py-1 border rounded">Copiar</button>
                    <span v-else class="text-gray-400 text-sm">—</span>
                  </div>
                </td>
                <td class="px-4 py-2 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-2">
                    <Link :href="`/ascensores/${a.id}`" class="text-blue-600 hover:text-blue-800 text-sm">Ver</Link>
                    <Link :href="`/ascensores/${a.id}/edit`" class="text-gray-700 hover:text-gray-900 text-sm">Editar</Link>
                    <button @click="eliminar(a.id)"
                            class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="p-8 text-center text-gray-500">
          No hay ascensores registrados.
        </div>

        <!-- Paginación -->
        <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
          <div class="text-sm text-gray-500">
            Mostrando {{ ascensores.from || 0 }}-{{ ascensores.to || 0 }} de {{ ascensores.total || 0 }}
          </div>
          <div class="space-x-2">
            <button :disabled="!ascensores.prev_page_url"
                    @click="router.visit(ascensores.prev_page_url, { preserveState: true })"
                    class="px-3 py-1 rounded border disabled:opacity-50">Anterior</button>
            <button :disabled="!ascensores.next_page_url"
                    @click="router.visit(ascensores.next_page_url, { preserveState: true })"
                    class="px-3 py-1 rounded border disabled:opacity-50">Siguiente</button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
