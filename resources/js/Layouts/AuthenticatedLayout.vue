<!-- resources/js/Layouts/AuthenticatedLayout.vue -->
<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth.user)
const showUserMenu = ref(false)
const showMobileMenu = ref(false)

function logout() {
  router.post('/logout')
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          
          <!-- Logo y navegación principal -->
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <Link href="/dashboard" class="text-xl font-bold text-blue-600 flex items-center">
                <svg class="h-8 w-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Ascensores SL
              </Link>
            </div>

            <!-- Links de navegación (Desktop) -->
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <Link href="/dashboard" 
                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition"
                    :class="$page.url === '/dashboard' 
                      ? 'border-blue-500 text-gray-900' 
                      : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'">
                Dashboard
              </Link>

              <Link href="/ascensores" 
                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition"
                    :class="$page.url.startsWith('/ascensores') 
                      ? 'border-blue-500 text-gray-900' 
                      : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'">
                Ascensores
              </Link>

              <Link href="/revisiones" 
                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition"
                    :class="$page.url.startsWith('/revisiones') 
                      ? 'border-blue-500 text-gray-900' 
                      : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'">
                Revisiones
              </Link>

              <Link href="/reportes" 
                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition"
                    :class="$page.url.startsWith('/reportes') 
                      ? 'border-blue-500 text-gray-900' 
                      : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'">
                Reportes
              </Link>
            </div>
          </div>

          <!-- Usuario y menú móvil -->
          <div class="flex items-center">
            <!-- Menú de usuario (Desktop) -->
            <div class="hidden sm:block relative">
              <button @click="showUserMenu = !showUserMenu"
                      class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none transition">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold mr-2">
                  {{ user?.name?.charAt(0).toUpperCase() }}
                </div>
                <span class="mr-2">{{ user?.name }}</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown -->
              <div v-show="showUserMenu" 
                   @click.away="showUserMenu = false"
                   class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                <div class="px-4 py-2 border-b border-gray-200">
                  <p class="text-sm font-medium text-gray-900">{{ user?.name }}</p>
                  <p class="text-xs text-gray-500">{{ user?.email }}</p>
                </div>
                
                <Link href="/perfil" 
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Mi Perfil
                </Link>
                
                <Link href="/configuracion" 
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Configuración
                </Link>
                
                <button @click="logout"
                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                  Cerrar Sesión
                </button>
              </div>
            </div>

            <!-- Botón menú móvil -->
            <button @click="showMobileMenu = !showMobileMenu"
                    class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!showMobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

        </div>
      </div>

      <!-- Menú móvil -->
      <div v-show="showMobileMenu" class="sm:hidden border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
          <Link href="/dashboard" 
                class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                :class="$page.url === '/dashboard' 
                  ? 'bg-blue-50 border-blue-500 text-blue-700' 
                  : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'">
            Dashboard
          </Link>

          <Link href="/ascensores" 
                class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                :class="$page.url.startsWith('/ascensores') 
                  ? 'bg-blue-50 border-blue-500 text-blue-700' 
                  : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'">
            Ascensores
          </Link>

          <Link href="/revisiones" 
                class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                :class="$page.url.startsWith('/revisiones') 
                  ? 'bg-blue-50 border-blue-500 text-blue-700' 
                  : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'">
            Revisiones
          </Link>

          <Link href="/reportes" 
                class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                :class="$page.url.startsWith('/reportes') 
                  ? 'bg-blue-50 border-blue-500 text-blue-700' 
                  : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'">
            Reportes
          </Link>
        </div>

        <!-- Usuario en móvil -->
        <div class="pt-4 pb-3 border-t border-gray-200">
          <div class="flex items-center px-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                {{ user?.name?.charAt(0).toUpperCase() }}
              </div>
            </div>
            <div class="ml-3">
              <div class="text-base font-medium text-gray-800">{{ user?.name }}</div>
              <div class="text-sm font-medium text-gray-500">{{ user?.email }}</div>
            </div>
          </div>
          <div class="mt-3 space-y-1">
            <Link href="/perfil" 
                  class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
              Mi Perfil
            </Link>
            <Link href="/configuracion" 
                  class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
              Configuración
            </Link>
            <button @click="logout"
                    class="block w-full text-left px-4 py-2 text-base font-medium text-red-600 hover:bg-red-50">
              Cerrar Sesión
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenido principal -->
    <main>
      <slot />
    </main>

    <!-- Footer (opcional) -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-sm text-gray-500">
          © {{ new Date().getFullYear() }} Sistema de Gestión de Ascensores - San Luis
        </p>
      </div>
    </footer>
  </div>
</template>