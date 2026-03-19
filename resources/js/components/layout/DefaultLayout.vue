<template>
  <div class="flex min-h-dvh w-full flex-col overflow-x-clip bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 lg:h-dvh lg:flex-row lg:overflow-hidden">
    <!-- Mobile top bar -->
    <header class="flex shrink-0 items-center justify-between border-b border-zinc-200/70 bg-white px-4 py-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 lg:hidden">
      <button class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" @click="sidebarOpen = true">☰</button>
      <h1 class="text-sm font-semibold text-zinc-900 dark:text-white">Venta Carros</h1>
      <div class="h-9 w-9"></div>
    </header>

    <!-- Sidebar -->
    <Sidebar :isOpen="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Main content area - scrollable only -->
    <main class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden overscroll-contain">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useBodyScrollLock } from '../../composables/useBodyScrollLock'
import Sidebar from './Sidebar.vue'

const sidebarOpen = ref(false)
const route = useRoute()
const isMobileNavigationOpen = computed(() => sidebarOpen.value)

useBodyScrollLock(isMobileNavigationOpen)

// Close sidebar when route changes
watch(() => route.path, () => {
  sidebarOpen.value = false
})
</script>
