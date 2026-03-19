<template>
    <DashboardPanel
        eyebrow="Portafolio"
        title="Estado del inventario"
        description="Resumen operativo del stock y del capital comprometido."
        :icon="TruckIcon"
    >
        <div class="space-y-3">
            <div
                v-for="item in items"
                :key="item.label"
                class="flex items-center justify-between rounded-2xl border border-zinc-200 px-4 py-3"
            >
                <div class="flex items-center gap-3">
                    <span class="h-2.5 w-2.5 rounded-full" :class="item.dotClass"></span>
                    <span class="text-sm text-zinc-600">{{ item.label }}</span>
                </div>
                <span class="text-base font-semibold tracking-tight text-zinc-950">{{ item.value }}</span>
            </div>
        </div>

        <div class="mt-6 rounded-3xl bg-zinc-50 px-4 py-4">
            <p class="text-xs uppercase tracking-[0.18em] text-zinc-400">Potencial estimado</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-zinc-950">{{ formatCurrency(potentialInventoryProfit) }}</p>
            <p class="mt-2 text-sm text-zinc-500">Ganancia potencial del inventario que ya tiene precio estimado cargado.</p>
        </div>
    </DashboardPanel>
</template>

<script setup>
import { TruckIcon } from '@heroicons/vue/24/outline';
import DashboardPanel from './DashboardPanel.vue';
import { formatCurrency } from './formatters';

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    potentialInventoryProfit: {
        type: Number,
        default: 0,
    },
});
</script>