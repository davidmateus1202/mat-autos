<template>
  <transition name="fade">
    <div v-if="visible" class="fixed inset-0 z-60 flex items-center justify-center bg-white/70 dark:bg-zinc-900/70 backdrop-blur">
      <div class="flex flex-col items-center gap-3">
        <svg class="animate-spin h-8 w-8 text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        <p class="text-sm text-zinc-700 dark:text-zinc-300">Cargando…</p>
      </div>
    </div>
  </transition>
</template>

<script setup>
const props = defineProps({ visible: { type: Boolean, default: false } })
</script>
