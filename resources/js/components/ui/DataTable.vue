<template>
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                <thead class="bg-zinc-50 dark:bg-zinc-950">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted dark:text-muted-dark"
                            :class="col.class"
                        >
                            {{ col.label }}
                        </th>
                        <th v-if="$slots.actions" scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                    <tr v-for="(item, index) in items" :key="item.id || index" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td
                            v-for="col in columns"
                            :key="col.key"
                            class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100"
                            :class="col.cellClass"
                        >
                            <slot :name="col.key" :item="item" :value="item[col.key]">
                                {{ item[col.key] }}
                            </slot>
                        </td>
                        <td v-if="$slots.actions" class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <slot name="actions" :item="item" />
                        </td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-12 text-center text-muted dark:text-muted-dark">
                            <slot name="empty">No data available</slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
defineProps({
    columns: {
        type: Array,
        required: true,
        // { key: 'name', label: 'Name', class: '', cellClass: '' }
    },
    items: {
        type: Array,
        default: () => [],
    },
});
</script>
