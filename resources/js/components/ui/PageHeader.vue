<template>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ title }}
            </h1>
            <nav v-if="breadcrumbs.length" class="mt-1 flex text-sm text-muted dark:text-muted-dark">
                <template v-for="(crumb, index) in breadcrumbs" :key="index">
                    <span v-if="index > 0" class="mx-2">/</span>
                    <span :class="{ 'font-medium text-zinc-900 dark:text-white': index === breadcrumbs.length - 1 }">
                        {{ crumb }}
                    </span>
                </template>
            </nav>
        </div>
        <div class="flex items-center gap-3">
            <slot name="actions" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
});
</script>
