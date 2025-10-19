<!-- resources/js/Pages/Ascensores/Create.vue -->
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const form = useForm({
  codigo_interno: '',
  edificio: '',
  direccion: '',
  numero_ascensor: '',
  descripcion: '',
  estado: 'activo', // activo | mantenimiento | inactivo
})

function submit() {
  form.post('/ascensores', {
    onSuccess: () => {
      // Redirige a index o muestra mensaje
    },
  })
}
</script>

<template>
  <Head title="Nuevo Ascensor" />
  <AuthenticatedLayout>
    <div class="py-8">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex items-center justify-between">
          <h1 class="text-2xl font-bold">Nuevo Ascensor</h1>
          <Link href="/ascensores" class="text-blue-600 hover:text-blue-800">Volver</Link>
        </div>

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-6 space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium">Código interno</label>
              <input v-model="form.codigo_interno" type="text"
                     class="w-full border rounded p-2" required>
              <p v-if="form.errors.codigo_interno" class="text-red-600 text-sm">
                {{ form.errors.codigo_interno }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium">Número de ascensor</label>
              <input v-model="form.numero_ascensor" type="text"
                     class="w-full border rounded p-2">
              <p v-if="form.errors.numero_ascensor" class="text-red-600 text-sm">
                {{ form.errors.numero_ascensor }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium">Edificio</label>
              <input v-model="form.edificio" type="text"
                     class="w-full border rounded p-2">
              <p v-if="form.errors.edificio" class="text-red-600 text-sm">
                {{ form.errors.edificio }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium">Dirección</label>
              <input v-model="form.direccion" type="text"
                     class="w-full border rounded p-2">
              <p v-if="form.errors.direccion" class="text-red-600 text-sm">
                {{ form.errors.direccion }}
              </p>
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium">Descripción</label>
              <textarea v-model="form.descripcion" rows="3"
                        class="w-full border rounded p-2"></textarea>
              <p v-if="form.errors.descripcion" class="text-red-600 text-sm">
                {{ form.errors.descripcion }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium">Estado</label>
              <select v-model="form.estado" class="w-full border rounded p-2">
                <option value="activo">Activo</option>
                <option value="mantenimiento">Mantenimiento</option>
                <option value="inactivo">Inactivo</option>
              </select>
              <p v-if="form.errors.estado" class="text-red-600 text-sm">
                {{ form.errors.estado }}
              </p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3">
            <Link href="/ascensores" class="px-4 py-2 rounded border">Cancelar</Link>
            <button type="submit" :disabled="form.processing"
                    class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">
              Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
