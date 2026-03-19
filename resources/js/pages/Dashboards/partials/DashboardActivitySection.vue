<template>
    <DashboardPanel
        :eyebrow="eyebrow"
        :title="title"
        :description="description"
        :icon="icon"
    >
        <div v-if="items.length" class="space-y-3">
            <div v-for="(item, index) in items" :key="itemKey(item, index)">
                <slot name="item" :item="item" />
            </div>
        </div>

        <div v-else class="rounded-3xl border border-dashed border-zinc-300 bg-zinc-50 px-6 py-12 text-center text-sm text-zinc-500">
            {{ emptyText }}
        </div>
    </DashboardPanel>
</template>

<script setup>
import DashboardPanel from './DashboardPanel.vue';

const props = defineProps({
    eyebrow: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    icon: {
        type: [Object, Function],
        default: null,
    },
    items: {
        type: Array,
        default: () => [],
    },
    keyField: {
        type: String,
        default: 'id',
    },
    emptyText: {
        type: String,
        default: 'No hay datos para mostrar.',
    },
});

function itemKey(item, index) {
    return item?.[props.keyField] ?? item?.id ?? item?.label ?? `${props.title}-${index}`;
}
</script>