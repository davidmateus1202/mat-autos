<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <div
            v-for="(item, index) in items"
            :key="item.id || index"
            class="card flex flex-col overflow-hidden p-4"
        >
            <!-- Header -->
            <div class="mb-3 flex items-start justify-between">
                <slot name="header" :item="item">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        #{{ item.id }}
                    </h3>
                </slot>
            </div>

            <!-- Body -->
            <div class="flex-1 space-y-2 text-sm text-muted dark:text-muted-dark">
                <slot name="body" :item="item" />
            </div>

            <!-- Footer/Actions -->
            <div v-if="$slots.actions" class="mt-4 flex items-center justify-end gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                <slot name="actions" :item="item" />
            </div>
        </div>
        
        <div v-if="items.length === 0" class="col-span-full">
            <slot name="empty">
                <div class="rounded-xl border border-dashed border-zinc-300 p-8 text-center text-muted dark:border-zinc-700 dark:text-muted-dark">
                    No items found
                </div>
            </slot>
        </div>
    </div>
</template>

<script setup>
defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});
</script>
