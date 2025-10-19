<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  empresa: Object,
  app: Object,
})

const form = useForm({
  empresa: {
    nombre: props.empresa?.nombre || '',
    direccion: props.empresa?.direccion || '',
    telefono: props.empresa?.telefono || '',
    email: props.empresa?.email || '',
  },
  app: {
    offline_enabled: props.app?.offline_enabled ?? true,
    checklist_categories: props.app?.checklist_categories || [],
    pdf_footer: props.app?.pdf_footer || '',
  }
})

const nuevaCat = ''
function addCat() {
  if (!form.app.checklist_categories.includes(nuevaCat) && nuevaCat.trim()) {
    form.app.checklist_categories.push(nuevaCat.trim())
  }
}
function removeCat(i) {
  form.app.checklist_categories.splice(i, 1)
}

function save() {
  form.put('/configuracion')
}
</script>

<template>
  <Head title="Configuración" />
  <AuthenticatedLayout>
    <div class="py-8">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 rounded shadow space-y-4">
          <h2 class="text-lg font-semibold">Datos de la empresa</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-sm">Nombre</label>
              <input v-model="form.empresa.nombre" type="text" class="w-full border rounded p-2">
              <p v-if="form.errors['empresa.nombre']" class="text-red-600 text-sm">
                {{ form.errors['empresa.nombre'] }}
              </p>
            </div>
            <div>
              <label class="text-sm">Email</label>
              <input v-model="form.empresa.email" type="email" class="w-full border rounded p-2">
              <p v-if="form.errors['empresa.email']" class="text-red-600 text-sm">
                {{ form.errors['empresa.email'] }}
              </p>
            </div>
            <div>
              <label class="text-sm">Dirección</label>
              <input v-model="form.empresa.direccion" type="text" class="w-full border rounded p-2">
            </div>
            <div>
              <label class="text-sm">Teléfono</label>
              <input v-model="form.empresa.telefono" type="text" class="w-full border rounded p-2">
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded shadow space-y-4">
          <h2 class="text-lg font-semibold">Aplicación</h2>
          <div class="flex items-center gap-2">
            <input id="offline" v-model="form.app.offline_enabled" type="checkbox" class="h-4 w-4">
            <label for="offline" class="text-sm">Habilitar modo offline (cola de sincronización)</label>
          </div>

          <div>
            <label class="text-sm">Categorías de checklist</label>
            <div class="flex flex-wrap gap-2 my-2">
              <span v-for="(c, i) in form.app.checklist_categories" :key="i"
                    class="px-2 py-1 text-xs bg-gray-100 rounded">
                {{ c }}
                <button type="button" class="ml-1 text-red-600" @click="removeCat(i)">x</button>
              </span>
            </div>
            <!-- input simple; puedes reemplazar por uno controlado -->
            <input id="newCat" type="text" placeholder="Nueva categoría"
                   class="border rounded p-2 !mr-2"
                   @keyup.enter="addCat">
            <button type="button" @click="addCat" class="px-3 py-1 bg-gray-100 rounded border">Agregar</button>
          </div>

          <div>
            <label class="text-sm">Pie de página PDF</label>
            <input v-model="form.app.pdf_footer" type="text" class="w-full border rounded p-2">
          </div>

          <div class="flex justify-end">
            <button @click="save" :disabled="form.processing"
                    class="px-4 py-2 bg-blue-600 text-white rounded">
              Guardar cambios
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
