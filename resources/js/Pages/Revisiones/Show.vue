<!-- resources/js/Pages/Revisiones/Show.vue -->
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SignaturePad from '@/Components/SignaturePad.vue'
import { computed } from 'vue'

const props = defineProps({
  revision: Object
})

const form = useForm({
  fecha: props.revision.fecha,          // Y-m-d
  estado: props.revision.estado,
  formulario: props.revision.formulario || {},
  observaciones: props.revision.observaciones || '',
  firma_tecnico_base64: '',             // se envía si se firma
  firma_cliente_base64: '',
  firma_tecnico_nombre: props.revision.firma_tecnico_nombre || '',
  firma_cliente_nombre: props.revision.firma_cliente_nombre || '',
})

const isCompletada = computed(() => form.estado === 'completada')

function guardar() {
  form.put(`/revisiones/${props.revision.id}`, {
    onSuccess: () => {},
  })
}

function marcarCompletada() {
  form.estado = 'completada'
  guardar()
}
</script>

<template>
  <Head :title="`Revisión #${revision.id}`" />
  <AuthenticatedLayout>
    <div class="py-8">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Encabezado -->
        <div class="flex items-start justify-between">
          <div>
            <h1 class="text-2xl font-bold">Revisión #{{ revision.id }}</h1>
            <p class="text-sm text-gray-600">
              Ascensor:
              <Link :href="`/ascensores/${revision.ascensor.id}`" class="text-blue-600">
                {{ revision.ascensor.codigo_interno }} — {{ revision.ascensor.edificio }}
              </Link>
            </p>
          </div>
          <div class="flex items-center gap-2">
            <span class="px-2 py-1 text-xs rounded-full"
              :class="{
                'bg-green-100 text-green-800': form.estado === 'completada',
                'bg-yellow-100 text-yellow-800': form.estado === 'pendiente'
              }">
              {{ form.estado }}
            </span>
            <a :href="`/a/${revision.ascensor.qr_slug}`" target="_blank"
               class="px-3 py-2 text-sm rounded border bg-white hover:bg-gray-50">
              Ver ficha pública
            </a>
          </div>
        </div>

        <!-- Form principal -->
        <div class="bg-white rounded shadow p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="text-sm">Fecha</label>
              <input v-model="form.fecha" type="date" class="w-full border rounded p-2">
              <p v-if="form.errors.fecha" class="text-red-600 text-sm">{{ form.errors.fecha }}</p>
            </div>

            <div>
              <label class="text-sm">Estado</label>
              <select v-model="form.estado" class="w-full border rounded p-2">
                <option value="pendiente">Pendiente</option>
                <option value="completada">Completada</option>
              </select>
              <p v-if="form.errors.estado" class="text-red-600 text-sm">{{ form.errors.estado }}</p>
            </div>

            <div class="md:col-span-1">
              <label class="text-sm">Técnico</label>
              <div class="border rounded p-2 bg-gray-50">
                {{ revision.tecnico?.name || '—' }} <span class="text-xs text-gray-500">({{ revision.tecnico?.email || '' }})</span>
              </div>
            </div>
          </div>

          <div>
            <label class="text-sm">Observaciones</label>
            <textarea v-model="form.observaciones" class="w-full border rounded p-2" rows="3"></textarea>
          </div>

          <!-- Firmas -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <div class="flex items-center justify-between">
                <label class="text-sm font-medium">Firma del técnico</label>
                <span v-if="revision.firmado_at" class="text-xs text-gray-500">Firmado: {{ revision.firmado_at }}</span>
              </div>

              <!-- Firma existente -->
              <div v-if="revision.firma_tecnico_url" class="mt-2">
                <img :src="revision.firma_tecnico_url" alt="Firma técnico" class="border rounded bg-white p-1 max-h-40">
                <div class="text-xs text-gray-500 mt-1">
                  {{ revision.firma_tecnico_nombre || 'Técnico' }}
                </div>
              </div>

              <!-- Nueva firma -->
              <input v-model="form.firma_tecnico_nombre" placeholder="Nombre del técnico" class="border rounded p-2 w-full mt-2">
              <SignaturePad v-model="form.firma_tecnico_base64" :width="500" :height="160" />
            </div>

            <div>
              <label class="text-sm font-medium">Firma del responsable (opcional)</label>

              <div v-if="revision.firma_cliente_url" class="mt-2">
                <img :src="revision.firma_cliente_url" alt="Firma responsable" class="border rounded bg-white p-1 max-h-40">
                <div class="text-xs text-gray-500 mt-1">
                  {{ revision.firma_cliente_nombre || 'Responsable' }}
                </div>
              </div>

              <input v-model="form.firma_cliente_nombre" placeholder="Nombre del responsable" class="border rounded p-2 w-full mt-2">
              <SignaturePad v-model="form.firma_cliente_base64" :width="500" :height="160" />
            </div>
          </div>

          <div class="flex items-center justify-end gap-2">
            <button type="button" @click="guardar"
                    :disabled="form.processing"
                    class="px-4 py-2 rounded border bg-white hover:bg-gray-50">
              Guardar cambios
            </button>
            <button type="button" @click="marcarCompletada"
                    :disabled="form.processing || isCompletada"
                    class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">
              Marcar como completada
            </button>
          </div>
          <p v-if="revision.verificacion_hash" class="text-[11px] text-gray-400 text-right">
            Código de verificación: {{ revision.verificacion_hash }}
          </p>
        </div>

        <div>
          <Link href="/revisiones" class="text-blue-600 hover:text-blue-800 text-sm">← Volver a Revisiones</Link>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
