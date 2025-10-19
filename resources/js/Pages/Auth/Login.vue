<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const form = useForm({
  email: '',
  password: '',
  remember: false
})

// Acceder a los errores compartidos
const errors = computed(() => usePage().props.errors)

function submit() {
  form.post('/login', {
    onFinish: () => {
      form.password = ''
    },
  })
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <form @submit.prevent="submit"
          class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-sm space-y-4">
      
      <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">
        Sistema de Ascensores
      </h1>
      
      <p class="text-center text-gray-600 text-sm mb-4">
        Iniciar sesión
      </p>

      <!-- Campo Email -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Correo electrónico
        </label>
        <input 
          v-model="form.email" 
          type="email"
          class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
          :class="{ 'border-red-500 bg-red-50': errors.email }"
          placeholder="admin@ejemplo.com"
          required
          autofocus
        >
        <p v-if="errors.email" class="text-red-600 text-sm mt-1">
          {{ errors.email[0] }}
        </p>
      </div>

      <!-- Campo Password -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Contraseña
        </label>
        <input 
          v-model="form.password" 
          type="password"
          class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
          :class="{ 'border-red-500 bg-red-50': errors.password }"
          placeholder="••••••••"
          required
        >
        <p v-if="errors.password" class="text-red-600 text-sm mt-1">
          {{ errors.password[0] }}
        </p>
      </div>

      <!-- Recordarme -->
      <div class="flex items-center">
        <input 
          v-model="form.remember" 
          type="checkbox" 
          id="remember"
          class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
        >
        <label for="remember" class="ml-2 block text-sm text-gray-700">
          Mantener sesión iniciada
        </label>
      </div>

      <!-- Botón Submit -->
      <button 
        type="submit"
        :disabled="form.processing"
        class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform active:scale-95"
      >
        <span v-if="form.processing" class="flex items-center justify-center">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Procesando...
        </span>
        <span v-else>Iniciar sesión</span>
      </button>
    </form>
  </div>
</template>
