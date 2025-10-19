<!-- resources/js/Pages/Dashboard.vue -->
<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
  stats: Object,
  ascensoresRecientes: Array,
  revisionesProximas: Array
})
</script>

<template>
  <Head title="Dashboard" />
  
  <AuthenticatedLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
          <p class="text-gray-600 mt-1">Sistema de Gestión de Ascensores - San Luis</p>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Ascensores -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Ascensores</dt>
                  <dd class="text-3xl font-semibold text-gray-900">{{ stats?.total || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>

          <!-- Activos -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Activos</dt>
                  <dd class="text-3xl font-semibold text-gray-900">{{ stats?.activos || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>

          <!-- Revisiones Pendientes -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Revisiones Vencidas</dt>
                  <dd class="text-3xl font-semibold text-gray-900">{{ stats?.pendientes || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>

          <!-- En Mantenimiento -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">En Mantenimiento</dt>
                  <dd class="text-3xl font-semibold text-gray-900">{{ stats?.mantenimiento || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <Link href="/ascensores/create" 
                class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition cursor-pointer group">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600">
                  Nuevo Ascensor
                </h3>
                <p class="text-sm text-gray-500 mt-1">Registrar un nuevo ascensor</p>
              </div>
              <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
          </Link>

          <Link href="/ascensores" 
                class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition cursor-pointer group">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600">
                  Ver Ascensores
                </h3>
                <p class="text-sm text-gray-500 mt-1">Gestionar todos los ascensores</p>
              </div>
              <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
            </div>
          </Link>

          <Link href="/revisiones" 
                class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition cursor-pointer group">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600">
                  Revisiones
                </h3>
                <p class="text-sm text-gray-500 mt-1">Programar y gestionar revisiones</p>
              </div>
              <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
            </div>
          </Link>
        </div>

        <!-- Dos columnas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          
          <!-- Ascensores Recientes -->
          <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
              <h2 class="text-lg font-semibold text-gray-900">Ascensores Recientes</h2>
              <Link href="/ascensores" class="text-sm text-blue-600 hover:text-blue-800">
                Ver todos →
              </Link>
            </div>
            <div class="p-6">
              <div v-if="ascensoresRecientes?.length" class="space-y-4">
                <div v-for="ascensor in ascensoresRecientes" :key="ascensor.id"
                     class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                  <div class="flex-1">
                    <div class="flex items-center gap-2">
                      <h3 class="font-medium text-gray-900">{{ ascensor.numero_identificador }}</h3>
                      <span 
                        class="px-2 py-1 text-xs rounded-full"
                        :class="{
                          'bg-green-100 text-green-800': ascensor.estado === 'activo',
                          'bg-red-100 text-red-800': ascensor.estado === 'mantenimiento',
                          'bg-gray-100 text-gray-800': ascensor.estado === 'inactivo'
                        }">
                               {{ ascensor.estado }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">{{ ascensor.edificio }}</p>
                    <p class="text-xs text-gray-400">{{ ascensor.direccion }}</p>
                  </div>
                  <Link :href="`/ascensores/${ascensor.id}`" 
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Ver detalles →
                  </Link>
                </div>
              </div>
              <div v-else class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="mt-2">No hay ascensores registrados</p>
                <Link href="/ascensores/create" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                  Registrar el primero
                </Link>
              </div>
            </div>
          </div>

          <!-- Próximas Revisiones -->
          <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
              <h2 class="text-lg font-semibold text-gray-900">Próximas Revisiones</h2>
              <Link href="/revisiones" class="text-sm text-blue-600 hover:text-blue-800">
                Ver todas →
              </Link>
            </div>
            <div class="p-6">
              <div v-if="revisionesProximas?.length" class="space-y-4">
                <div v-for="revision in revisionesProximas" :key="revision.id"
                     class="flex items-center justify-between p-4 border-l-4 rounded"
                     :class="{
                       'border-red-500 bg-red-50': revision.dias_restantes < 0,
                       'border-yellow-500 bg-yellow-50': revision.dias_restantes >= 0 && revision.dias_restantes <= 7,
                       'border-green-500 bg-green-50': revision.dias_restantes > 7
                     }">
                  <div class="flex-1">
                    <h3 class="font-medium text-gray-900">{{ revision.ascensor?.numero_identificador }}</h3>
                    <p class="text-sm text-gray-600">{{ revision.ascensor?.edificio }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                      <span class="font-medium">{{ revision.fecha_programada }}</span>
                      <span v-if="revision.dias_restantes < 0" class="text-red-600 font-medium ml-2">
                        ⚠️ Vencida hace {{ Math.abs(revision.dias_restantes) }} días
                      </span>
                      <span v-else-if="revision.dias_restantes === 0" class="text-yellow-600 font-medium ml-2">
                        🔔 Hoy
                      </span>
                      <span v-else-if="revision.dias_restantes <= 7" class="text-yellow-600 font-medium ml-2">
                        ⏰ En {{ revision.dias_restantes }} días
                      </span>
                      <span v-else class="text-gray-600 ml-2">
                        En {{ revision.dias_restantes }} días
                      </span>
                    </p>
                  </div>
                  <Link :href="`/revisiones/${revision.id}`" 
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium whitespace-nowrap">
                    Realizar →
                  </Link>
                </div>
              </div>
              <div v-else class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <p class="mt-2">No hay revisiones programadas</p>
                <Link href="/revisiones/create" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                  Programar revisión
                </Link>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
