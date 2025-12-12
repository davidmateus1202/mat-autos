<template>
    <div class="animate-pulse space-y-4">
        <div v-for="i in rows" :key="i" class="flex items-center space-x-4">
            <div class="h-12 w-12 rounded-full bg-zinc-200 dark:bg-zinc-700"></div>
            <div class="flex-1 space-y-2 py-1">
                <div class="h-4 rounded bg-zinc-200 dark:bg-zinc-700"></div>
                <div class="space-y-2">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2 h-4 rounded bg-zinc-200 dark:bg-zinc-700"></div>
                        <div class="col-span-1 h-4 rounded bg-zinc-200 dark:bg-zinc-700"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    rows: {
        type: Number,
        default: 3,
    },
});
</script>
