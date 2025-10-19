<!-- resources/js/Pages/Ascensores/Show.vue -->
<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed, onMounted } from 'vue'

const props = defineProps({
  ascensor: Object,
  revisiones: Array,
})

const publicUrl = computed(() => props.ascensor?.qr_public_url || '')

onMounted(() => {
  // Debug rápido: verifica que vengan las props correctas
  const page = usePage()
  console.log('Inertia props:', page.props)
})
</script>

<template>
  <Head :title="`Ascensor ${ascensor?.codigo_interno || ascensor?.numero_ascensor || ''}`" />
  <AuthenticatedLayout>
    <div class="py-8">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="flex items-start justify-between">
          <div>
            <h1 class="text-2xl font-bold">
              Ascensor {{ ascensor?.codigo_interno || ascensor?.numero_ascensor || '—' }}
            </h1>
            <p class="text-gray-600">
              {{ ascensor?.edificio || '—' }} — {{ ascensor?.direccion || '—' }}
            </p>
            <div class="mt-2">
              <span class="px-2 py-1 text-xs rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': ascensor?.estado === 'activo',
                      'bg-red-100 text-red-800': ascensor?.estado === 'mantenimiento',
                      'bg-gray-100 text-gray-800': ascensor?.estado === 'inactivo'
                    }">
                {{ ascensor?.estado || '—' }}
              </span>
            </div>
          </div>

          <div class="flex flex-wrap gap-2">
            <a v-if="publicUrl" :href="publicUrl" target="_blank"
               class="px-3 py-2 rounded border bg-white hover:bg-gray-50">
              Ver ficha pública (QR)
            </a>
            <Link :href="`/revisiones/create?ascensor_id=${ascensor?.id}`"
                  class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
              Nueva revisión
            </Link>
            <Link :href="`/ascensores/${ascensor?.id}/edit`"
                  class="px-3 py-2 rounded border bg-white hover:bg-gray-50">
              Editar
            </Link>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="md:col-span-2 bg-white rounded shadow p-6 space-y-3">
            <h2 class="font-semibold text-gray-900 mb-2">Información</h2>
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
              <div><div class="text-gray-500">Código interno</div><div class="font-medium">{{ ascensor?.codigo_interno || '—' }}</div></div>
              <div><div class="text-gray-500">N° de ascensor</div><div class="font-medium">{{ ascensor?.numero_ascensor || '—' }}</div></div>
              <div><div class="text-gray-500">Edificio</div><div class="font-medium">{{ ascensor?.edificio || '—' }}</div></div>
              <div><div class="text-gray-500">Dirección</div><div class="font-medium">{{ ascensor?.direccion || '—' }}</div></div>
            </div>
            <div>
              <div class="text-gray-500 text-sm">Descripción</div>
              <div class="mt-1">{{ ascensor?.descripcion || '—' }}</div>
            </div>
          </div>

          <div class="bg-white rounded shadow p-6">
            <h2 class="font-semibold text-gray-900 mb-3">QR público</h2>
            <div class="flex flex-col items-center" v-if="publicUrl">
              <img
                :src="`https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(publicUrl)}`"
                alt="QR público"
                class="border rounded p-2 bg-white" />
              <a :href="publicUrl" target="_blank"
                 class="mt-3 text-sm text-blue-600 hover:text-blue-800 break-all">
                {{ publicUrl }}
              </a>
            </div>
            <div v-else class="text-sm text-gray-500">
              No hay QR asignado todavía.
            </div>
          </div>
        </div>

        <div class="bg-white rounded shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Últimas revisiones</h2>
            <Link href="/revisiones" class="text-sm text-blue-600 hover:text-blue-800">Ver todas →</Link>
          </div>

          <div v-if="revisiones?.length" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                  <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                  <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Técnico</th>
                  <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Observaciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="r in revisiones" :key="r.id" class="hover:bg-gray-50">
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
                  <td class="px-4 py-2">{{ r.tecnico || '—' }}</td>
                  <td class="px-4 py-2">{{ r.observaciones || '—' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="p-8 text-center text-gray-500">
            No hay revisiones registradas aún.
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
