<template>
  <div class="h-screen flex flex-col lg:flex-row bg-gray-50 dark:bg-zinc-900" :class="{ 'overflow-hidden': sidebarOpen }">
    <!-- Mobile top bar -->
    <header class="lg:hidden flex-shrink-0 flex items-center justify-between bg-white dark:bg-zinc-900 px-4 py-3 shadow-sm ring-1 ring-zinc-200/60 dark:ring-zinc-800 z-50">
      <button class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-zinc-100 dark:bg-zinc-800" @click="sidebarOpen = true">☰</button>
      <h1 class="text-sm font-semibold text-zinc-900 dark:text-white">Venta Carros</h1>
      <div class="h-9 w-9"></div>
    </header>

    <!-- Sidebar -->
    <Sidebar :isOpen="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Main content area - scrollable only -->
    <main class="flex-1 overflow-y-auto overflow-x-hidden">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './Sidebar.vue'

const sidebarOpen = ref(false)
const route = useRoute()

// Close sidebar when route changes
watch(() => route.path, () => {
  sidebarOpen.value = false
})
</script>
