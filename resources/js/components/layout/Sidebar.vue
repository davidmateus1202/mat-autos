<template>
  <aside :class="['fixed lg:static z-40 lg:z-auto inset-0 lg:inset-auto', isOpen ? 'block' : 'hidden', 'lg:block']" class="transition-all duration-200 lg:transition-none">
    <!-- Overlay for mobile -->
    <div v-if="isOpen" class="fixed inset-0 bg-black/40 lg:hidden z-30" @click="closeMenu"></div>

    <!-- Sidebar panel -->
    <div class="fixed lg:static top-0 left-0 h-screen w-72 bg-white dark:bg-zinc-900 shadow-xl ring-1 ring-zinc-200/60 dark:ring-zinc-800 rounded-r-2xl overflow-y-auto flex flex-col z-40">
      <!-- Header -->
      <div class="flex items-center justify-between px-4 py-4 border-b border-zinc-200 dark:border-zinc-800">
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
      <nav class="px-4 py-2 space-y-1 flex-1">
        <RouterLink :to="{ name: 'dashboard' }" class="nav-item" :class="{ 'active': isActive('dashboard') }" @click="closeMenu">
          <span class="icon">
            <HomeIcon class="h-4 w-4" />
          </span>
          <span class="label">Dashboard</span>
        </RouterLink>
        <RouterLink :to="{ name: 'cars.index' }" class="nav-item" :class="{ 'active': isActive('cars.index') }" @click="closeMenu">
          <span class="icon">
            <TruckIcon class="h-4 w-4" />
          </span>
          <span class="label">Cars</span>
        </RouterLink>
        <RouterLink :to="{ name: 'finance.index' }" class="nav-item" :class="{ 'active': isActive('finance.index') }" @click="closeMenu">
          <span class="icon">
            <CreditCardIcon class="h-4 w-4" />
          </span>
          <span class="label">Finance</span>
        </RouterLink>
      </nav>

      <!-- Footer -->
      <div class="px-4 py-4 border-t border-zinc-200 dark:border-zinc-800">
        <div class="flex items-center justify-between rounded-lg border border-zinc-200 dark:border-zinc-800 px-3 py-2">
          <span class="text-xs text-zinc-600">Theme</span>
          <div class="flex items-center gap-2">
            <button class="theme-btn" @click="setTheme('light')">Light</button>
            <button class="theme-btn" @click="setTheme('dark')">Dark</button>
          </div>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { HomeIcon, TruckIcon, CreditCardIcon, SparklesIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({ isOpen: { type: Boolean, default: false } })
const emit = defineEmits(['close'])
const route = useRoute()

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
</script>

<style scoped>
@reference "tailwindcss";
.nav-item { @apply flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800; }
.nav-item.active { @apply bg-zinc-100 dark:bg-zinc-800 font-medium; }
.nav-item .icon { @apply inline-flex h-7 w-7 items-center justify-center rounded-md bg-zinc-900 text-white text-xs; }
.nav-item .label { @apply truncate; }
.theme-btn { @apply inline-flex h-7 items-center rounded-md bg-zinc-100 dark:bg-zinc-800 px-3 text-xs font-medium text-zinc-700 dark:text-zinc-200 hover:opacity-90; }
</style>
