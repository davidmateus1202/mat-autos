<template>
    <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-zinc-300 bg-zinc-50 px-6 py-12 text-center dark:border-zinc-700 dark:bg-zinc-900/50">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
            <slot name="icon">
                <svg class="h-6 w-6 text-muted dark:text-muted-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </slot>
        </div>
        <h3 class="mt-2 text-sm font-semibold text-zinc-900 dark:text-white">{{ title }}</h3>
        <p class="mt-1 text-sm text-muted dark:text-muted-dark">{{ description }}</p>
        <div class="mt-6">
            <slot name="action" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: 'No items found',
    },
    description: {
        type: String,
        default: 'Get started by creating a new item.',
    },
});
</script>
