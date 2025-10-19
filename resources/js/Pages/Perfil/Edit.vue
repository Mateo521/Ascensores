<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({ user: Object })

const formProfile = useForm({
  name: props.user.name || '',
  email: props.user.email || '',
})
const formPassword = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const saveProfile = () => formProfile.put('/perfil')
const savePassword = () => formPassword.put('/perfil/password', {
  onSuccess: () => formPassword.reset('current_password','password','password_confirmation')
})
</script>

<template>
  <Head title="Mi Perfil" />
  <AuthenticatedLayout>
    <div class="py-8">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="bg-white p-6 rounded shadow">
          <h2 class="text-lg font-semibold mb-4">Datos de perfil</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-sm">Nombre</label>
              <input v-model="formProfile.name" type="text" class="w-full border rounded p-2">
              <p v-if="formProfile.errors.name" class="text-red-600 text-sm">{{ formProfile.errors.name }}</p>
            </div>
            <div>
              <label class="text-sm">Email</label>
              <input v-model="formProfile.email" type="email" class="w-full border rounded p-2">
              <p v-if="formProfile.errors.email" class="text-red-600 text-sm">{{ formProfile.errors.email }}</p>
            </div>
            <div>
              <label class="text-sm">Rol</label>
              <input :value="props.user.rol" type="text" class="w-full border rounded p-2 bg-gray-100" disabled>
            </div>
          </div>
          <div class="mt-4 flex justify-end">
            <button @click="saveProfile" :disabled="formProfile.processing"
                    class="px-4 py-2 bg-blue-600 text-white rounded">
              Guardar
            </button>
          </div>
        </div>

        <div class="bg-white p-6 rounded shadow">
          <h2 class="text-lg font-semibold mb-4">Cambiar contraseña</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="text-sm">Contraseña actual</label>
              <input v-model="formPassword.current_password" type="password" class="w-full border rounded p-2">
              <p v-if="formPassword.errors.current_password" class="text-red-600 text-sm">
                {{ formPassword.errors.current_password }}
              </p>
            </div>
            <div>
              <label class="text-sm">Nueva contraseña</label>
              <input v-model="formPassword.password" type="password" class="w-full border rounded p-2">
              <p v-if="formPassword.errors.password" class="text-red-600 text-sm">
                {{ formPassword.errors.password }}
              </p>
            </div>
            <div>
              <label class="text-sm">Confirmar nueva contraseña</label>
              <input v-model="formPassword.password_confirmation" type="password" class="w-full border rounded p-2">
            </div>
          </div>
          <div class="mt-4 flex justify-end">
            <button @click="savePassword" :disabled="formPassword.processing"
                    class="px-4 py-2 bg-blue-600 text-white rounded">
              Actualizar contraseña
            </button>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
