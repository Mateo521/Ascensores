<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  empresa: Object,
  app: Object,
})

const form = useForm({
  empresa: {
    nombre: props.empresa?.nombre || '',
    cuit: props.empresa?.cuit || '',
    inicio_actividad: props.empresa?.inicio_actividad || '',
    telefono: props.empresa?.telefono || '',
    email: props.empresa?.email || '',
    direccion: props.empresa?.direccion || '',
  },
  app: {
    offline_enabled: props.app?.offline_enabled ?? true,
    checklist_categories: props.app?.checklist_categories || [],
    pdf_footer: props.app?.pdf_footer || '',
  },
  logo: null, // archivo
})

const newCat = ref('')
const logoPreview = ref(props.empresa?.logo_url || null)

function addCat() {
  const v = newCat.value.trim()
  if (!v) return
  if (!form.app.checklist_categories.includes(v)) {
    form.app.checklist_categories.push(v)
  }
  newCat.value = ''
}
function removeCat(i) {
  form.app.checklist_categories.splice(i, 1)
}

function onLogoChange(e) {
  const file = e.target.files[0]
  form.logo = file || null
  if (file) {
    const reader = new FileReader()
    reader.onload = ev => (logoPreview.value = ev.target.result)
    reader.readAsDataURL(file)
  } else {
    logoPreview.value = null
  }
}

function save() {
  // Si NO hay archivo, puedes enviar JSON puro por PUT (más simple)
  if (!form.logo) {
    form.put('/configuracion') // enviará application/json, Laravel procesa arrays anidados perfecto
    return
  }

  // Si HAY archivo (logo), construimos FormData con bracket notation
  form.transform(data => {
    const fd = new FormData()

    // empresa[...]
    Object.entries(data.empresa || {}).forEach(([k, v]) => {
      if (v !== undefined && v !== null) fd.append(`empresa[${k}]`, v)
    })

    // app[...] (boolean -> 1/0 para backend)
    fd.append('app[offline_enabled]', data.app?.offline_enabled ? 1 : 0)
    ;(data.app?.checklist_categories || []).forEach((cat, i) => {
      fd.append(`app[checklist_categories][${i}]`, cat)
    })
    fd.append('app[pdf_footer]', data.app?.pdf_footer ?? '')

    // archivo logo
    if (data.logo) fd.append('logo', data.logo)

    // Método PUT (spoofing)
    fd.append('_method', 'PUT')

    return fd
  }).post('/configuracion', {
    // No pongas forceFormData aquí porque ya devolvimos un FormData manualmente
    onSuccess: () => {
      // feedback opcional
    }
  })
}
</script>

<template>
  <Head title="Configuración" />
  <AuthenticatedLayout>
    <div class="py-8">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Datos Empresa -->
        <div class="bg-white p-6 rounded shadow space-y-4">
          <h2 class="text-lg font-semibold">Datos de la empresa</h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-sm">Nombre</label>
              <input v-model="form.empresa.nombre" type="text" class="w-full border rounded p-2" />
              <p v-if="form.errors['empresa.nombre']" class="text-red-600 text-sm">{{ form.errors['empresa.nombre'] }}</p>
            </div>
            <div>
              <label class="text-sm">CUIT</label>
              <input v-model="form.empresa.cuit" type="text" class="w-full border rounded p-2" />
              <p v-if="form.errors['empresa.cuit']" class="text-red-600 text-sm">{{ form.errors['empresa.cuit'] }}</p>
            </div>
            <div>
              <label class="text-sm">Inicio de actividad</label>
              <input v-model="form.empresa.inicio_actividad" type="date" class="w-full border rounded p-2" />
              <p v-if="form.errors['empresa.inicio_actividad']" class="text-red-600 text-sm">{{ form.errors['empresa.inicio_actividad'] }}</p>
            </div>
            <div>
              <label class="text-sm">Teléfono</label>
              <input v-model="form.empresa.telefono" type="text" class="w-full border rounded p-2" />
              <p v-if="form.errors['empresa.telefono']" class="text-red-600 text-sm">{{ form.errors['empresa.telefono'] }}</p>
            </div>
            <div>
              <label class="text-sm">Email</label>
              <input v-model="form.empresa.email" type="email" class="w-full border rounded p-2" />
              <p v-if="form.errors['empresa.email']" class="text-red-600 text-sm">{{ form.errors['empresa.email'] }}</p>
            </div>
            <div>
              <label class="text-sm">Dirección</label>
              <input v-model="form.empresa.direccion" type="text" class="w-full border rounded p-2" />
              <p v-if="form.errors['empresa.direccion']" class="text-red-600 text-sm">{{ form.errors['empresa.direccion'] }}</p>
            </div>
          </div>

          <div class="mt-2">
            <label class="text-sm block mb-1">Logo (opcional)</label>
            <input type="file" accept="image/*" @change="onLogoChange" />
            <div v-if="logoPreview" class="mt-2">
              <img :src="logoPreview" alt="Logo" class="h-16 object-contain border rounded bg-white p-1" />
            </div>
          </div>

          <div class="flex justify-end">
            <button @click="save" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded">
              Guardar cambios
            </button>
          </div>
        </div>

        <!-- Config App -->
        <div class="bg-white p-6 rounded shadow space-y-4">
          <h2 class="text-lg font-semibold">Aplicación</h2>

          <div class="flex items-center gap-2">
            <input id="offline" v-model="form.app.offline_enabled" type="checkbox" class="h-4 w-4" />
            <label for="offline" class="text-sm">Habilitar modo offline (cola de sincronización)</label>
          </div>

          <div>
            <label class="text-sm">Categorías de checklist</label>
            <div class="flex flex-wrap gap-2 my-2">
              <span v-for="(c, i) in form.app.checklist_categories" :key="i" class="px-2 py-1 text-xs bg-gray-100 rounded">
                {{ c }}
                <button type="button" class="ml-1 text-red-600" @click="removeCat(i)">x</button>
              </span>
            </div>

            <div class="flex gap-2">
              <input v-model="newCat" id="newCat" type="text" placeholder="Nueva categoría" class="border rounded p-2 flex-1" @keyup.enter="addCat" />
              <button type="button" @click="addCat" class="px-3 py-1 bg-gray-100 rounded border">Agregar</button>
            </div>
          </div>

          <div>
            <label class="text-sm">Pie de página PDF</label>
            <input v-model="form.app.pdf_footer" type="text" class="w-full border rounded p-2" />
          </div>

          <div class="flex justify-end">
            <button @click="save" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded">
              Guardar cambios
            </button>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
