<template>
  <!-- Overlay for mobile -->
  <div v-if="isOpen" class="fixed inset-0 bg-black/40 lg:hidden z-30" @click="closeMenu"></div>

  <!-- Sidebar panel -->
  <div :class="['w-72 bg-white dark:bg-zinc-900 shadow-xl ring-1 ring-zinc-200/60 dark:ring-zinc-800 rounded-r-2xl overflow-y-auto flex flex-col', isOpen ? 'fixed inset-y-0 left-0 top-0 z-50 lg:hidden h-screen' : 'hidden lg:flex lg:static lg:flex-col h-screen z-40']">
      <!-- Header -->
      <div class="flex items-center justify-between px-4 py-4 border-b border-zinc-200 dark:border-zinc-800 shrink-0">
        <div class="flex items-center gap-2">
          <div class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-zinc-900 text-white">
            <SparklesIcon class="h-6 w-6" />
          </div>
          <div>
            <p class="text-sm font-semibold text-zinc-900 dark:text-white">Venta Carros</p>
            <p class="text-xs text-zinc-500">Dashboard</p>
          </div>
        </div>
        <button class="lg:hidden inline-flex h-8 w-8 items-center justify-center rounded-md bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700" @click="closeMenu">
          <XMarkIcon class="h-5 w-5" />
        </button>
      </div>

      <!-- Nav -->
      <nav class="px-3 py-2 space-y-1 flex-1 overflow-y-auto">
        <RouterLink :to="{ name: 'dashboard' }" class="nav-item" :class="{ 'active': isActive('dashboard') }" @click="closeMenu">
          <span class="icon">
            <HomeIcon class="h-4 w-4" />
          </span>
          <span class="label text-xs sm:text-sm">Inicio</span>
        </RouterLink>
        <RouterLink :to="{ name: 'cars.index' }" class="nav-item" :class="{ 'active': isActive('cars.index') }" @click="closeMenu">
          <span class="icon">
            <TruckIcon class="h-4 w-4" />
          </span>
          <span class="label text-xs sm:text-sm">Vehículos</span>
        </RouterLink>
        <RouterLink :to="{ name: 'finance.index' }" class="nav-item" :class="{ 'active': isActive('finance.index') }" @click="closeMenu">
          <span class="icon">
            <CreditCardIcon class="h-4 w-4" />
          </span>
          <span class="label text-xs sm:text-sm">Finanzas</span>
        </RouterLink>
        <RouterLink :to="{ name: 'profile' }" class="nav-item" :class="{ 'active': isActive('profile') }" @click="closeMenu">
          <span class="icon">
            <UserCircleIcon class="h-4 w-4" />
          </span>
          <span class="label text-xs sm:text-sm">Perfil</span>
        </RouterLink>
      </nav>

      <!-- Footer -->
      <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-800 space-y-2 shrink-0 overflow-y-auto max-h-32">
        <button @click="handleLogout" class="w-full flex items-center gap-2 px-2 py-2 rounded-lg text-xs sm:text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors cursor-pointer">
            <ArrowRightOnRectangleIcon class="h-4 w-4 flex-shrink-0" />
            <span class="truncate">Cerrar Sesión</span>
        </button>

        <div class="flex items-center justify-between rounded-lg border border-zinc-200 dark:border-zinc-800 px-2 py-2 text-xs">
          <span class="text-zinc-600 dark:text-zinc-400">Tema</span>
          <div class="flex items-center gap-1">
            <button class="theme-btn" @click="setTheme('light')">C</button>
            <button class="theme-btn" @click="setTheme('dark')">O</button>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { HomeIcon, TruckIcon, CreditCardIcon, SparklesIcon, XMarkIcon, ArrowRightOnRectangleIcon, UserCircleIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '../../stores/useAuth'

const props = defineProps({ isOpen: { type: Boolean, default: false } })
const emit = defineEmits(['close'])
const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

function isActive(name) {
  return route.name === name
}

function closeMenu() {
  emit('close')
}

function setTheme(mode) {
  const html = document.documentElement
  if (mode === 'dark') html.classList.add('dark')
  else html.classList.remove('dark')
}

const handleLogout = async () => {
    await authStore.logout()
    router.push({ name: 'login' })
}
</script>

<style scoped>
.nav-item { display: flex; align-items: center; gap: 0.75rem; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; color: #3f3f46; }
.dark .nav-item { color: #e4e4e7; }
.nav-item:hover { background-color: #f4f4f5; }
.dark .nav-item:hover { background-color: #27272a; }

.nav-item.active { background-color: #f4f4f5; font-weight: 500; }
.dark .nav-item.active { background-color: #27272a; }

.nav-item .icon { display: inline-flex; height: 1.75rem; width: 1.75rem; align-items: center; justify-content: center; border-radius: 0.375rem; background-color: #18181b; color: white; font-size: 0.75rem; }

.nav-item .label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.theme-btn { display: inline-flex; height: 1.75rem; align-items: center; border-radius: 0.375rem; background-color: #f4f4f5; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 500; color: #3f3f46; }
.dark .theme-btn { background-color: #27272a; color: #e4e4e7; }
.theme-btn:hover { opacity: 0.9; }
</style>
