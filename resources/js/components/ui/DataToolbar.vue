<template>
    <div class="mb-4 flex flex-col gap-4 rounded-lg bg-white p-4 shadow-sm ring-1 ring-zinc-200/70 dark:bg-zinc-900 dark:ring-zinc-800 md:flex-row md:items-center md:justify-between">
        <!-- Search -->
        <div class="relative w-full md:max-w-xs">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-muted dark:text-muted-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                type="text"
                class="block w-full rounded-lg border-zinc-300 bg-zinc-50 pl-10 text-sm focus:border-primary focus:ring-primary dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
                :placeholder="placeholder"
            />
        </div>

        <!-- Filters & Actions -->
        <div class="flex flex-wrap items-center gap-3">
            <slot name="filters" />
            <slot name="actions" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    modelValue: String,
    placeholder: {
        type: String,
        default: 'Search...',
    },
});

defineEmits(['update:modelValue']);
</script>
