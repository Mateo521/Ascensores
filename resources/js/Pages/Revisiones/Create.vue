<!-- resources/js/Pages/Revisiones/Create.vue -->
<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { reactive, computed, watch } from 'vue'
import { useOfflineQueue } from '@/composables/useOfflineQueue.js'
import axios from 'axios'

const props = defineProps({
  ascensor: Object,     // cuando viene ascensor_id
  ascensores: Array,    // cuando NO viene ascensor_id
  hoy: String,
  hora: String,
})

const { enqueueRevision, syncAll } = useOfflineQueue()
const online = computed(() => navigator.onLine)

const form = reactive({
  ascensor_id: props.ascensor?.id || null,
  fecha: props.hoy,
  hora: props.hora,
  estado: 'completada',
  formulario: {
    numero_serie: '',
    marca: '',
    proveedor: '',
    piso_estacion: '',
    mantener_estructura: true,
    visitas: [
      { mitad: 'primera', fecha: props.hoy, hora: props.hora, medidas: [], nivel: 'A', completado: true }
    ],
    personal: '',
    carga_trabajo: 'media',
    en_programa: true,
    verificador: '',
    observacion: '',
  },
  observaciones: '',
})

const selectedAscensorId = reactive({ value: form.ascensor_id })

watch(() => selectedAscensorId.value, (val) => {
  form.ascensor_id = val
})

async function guardar() {
  try {
    if (!form.ascensor_id) {
      alert('Seleccione un ascensor')
      return
    }

    const payload = {
      ascensor_id: form.ascensor_id,
      fecha: form.fecha,
      estado: form.estado,
      observaciones: form.observaciones,
      formulario: {
        ...form.formulario,
        visitas: [{
          ...form.formulario.visitas[0],
          fecha: form.fecha,
          hora: form.hora,
          completado: form.estado === 'completada'
        }]
      }
    }

    if (online.value) {
      await axios.post('/revisiones', payload)
      alert('Revisión guardada (online)')
      window.location.href = '/revisiones'
    } else {
      await enqueueRevision({ temp_id: crypto.randomUUID(), ...payload })
      alert('Sin conexión. Revisión guardada offline y se sincronizará luego.')
      window.location.href = '/revisiones'
    }
  } catch (e) {
    console.error(e)
    alert('Error al guardar la revisión')
  }
}

async function sincronizarPendientes() {
  try {
    const res = await syncAll()
    alert(`Sincronización OK: ${res.count} registros.`)
    window.location.reload()
  } catch (e) {
    alert('No se pudo sincronizar: ' + e.message)
  }
}
</script>

<template>
  <Head title="Nueva revisión" />
  <AuthenticatedLayout>
    <div class="py-8">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex items-center justify-between">
          <h1 class="text-2xl font-bold">Nueva revisión</h1>
          <div class="flex gap-2 items-center">
            <span class="text-sm px-2 py-1 rounded"
                  :class="online ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
              {{ online ? 'Online' : 'Offline' }}
            </span>
            <button @click="sincronizarPendientes"
                    class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded border">
              Sincronizar pendientes
            </button>
          </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
          <!-- Selector de ascensor cuando no viene pre-cargado -->
          <div v-if="!form.ascensor_id">
            <label class="block text-sm font-medium">Seleccionar ascensor</label>
            <select v-model="selectedAscensorId.value" class="w-full border rounded p-2">
              <option value="">-- Seleccione --</option>
              <option v-for="a in ascensores" :key="a.id" :value="a.id">
                {{ a.codigo_interno }} - {{ a.edificio }} ({{ a.direccion }})
              </option>
            </select>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium">Fecha</label>
              <input v-model="form.fecha" type="date" class="w-full border rounded p-2">
            </div>
            <div>
              <label class="block text-sm font-medium">Hora</label>
              <input v-model="form.hora" type="time" class="w-full border rounded p-2">
            </div>
            <div>
              <label class="block text-sm font-medium">Estado</label>
              <select v-model="form.estado" class="w-full border rounded p-2">
                <option value="completada">Completada</option>
                <option value="pendiente">Pendiente</option>
              </select>
            </div>
          </div>

          <hr>

          <!-- Resto del formulario (igual que ya tenías) -->
          <!-- ... campos: numero_serie, marca, proveedor, etc ... -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium">Número de serie</label>
              <input v-model="form.formulario.numero_serie" type="text" class="w-full border rounded p-2">
            </div>
            <div>
              <label class="block text-sm font-medium">Marca</label>
              <input v-model="form.formulario.marca" type="text" class="w-full border rounded p-2">
            </div>
            <div>
              <label class="block text-sm font-medium">Proveedor</label>
              <input v-model="form.formulario.proveedor" type="text" class="w-full border rounded p-2">
            </div>
            <div>
              <label class="block text-sm font-medium">Piso/Estación</label>
              <input v-model="form.formulario.piso_estacion" type="text" class="w-full border rounded p-2">
            </div>
            <div class="flex items-center gap-2">
              <input id="mantener" v-model="form.formulario.mantener_estructura" type="checkbox" class="h-4 w-4">
              <label for="mantener" class="text-sm">Mantener la estructura</label>
            </div>
          </div>

          <div class="border rounded p-4">
            <h3 class="font-medium mb-3">Visita (mitad del mes)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium">Mitad</label>
                <select v-model="form.formulario.visitas[0].mitad" class="w-full border rounded p-2">
                  <option value="primera">Primera</option>
                  <option value="segunda">Segunda</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium">Nivel</label>
                <select v-model="form.formulario.visitas[0].nivel" class="w-full border rounded p-2">
                  <option value="A">A</option>
                  <option value="B">B</option>
                  <option value="C">C</option>
                </select>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium">Observaciones (registro)</label>
            <textarea v-model="form.observaciones" rows="2" class="w-full border rounded p-2"></textarea>
          </div>

          <div class="flex items-center justify-end gap-3">
            <Link href="/revisiones" class="px-4 py-2 rounded border">Cancelar</Link>
            <button @click="guardar" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
              Guardar
            </button>
          </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
