<template>
    <DashboardPanel
        eyebrow="Oportunidades"
        title="Vehículos con mejor margen"
        description="Inventario disponible con mayor potencial según el precio estimado guardado."
        :icon="ScaleIcon"
    >
        <div v-if="items.length" class="space-y-4">
            <RouterLink
                v-for="car in items"
                :key="car.id"
                :to="{ name: 'cars.show', params: { id: car.id } }"
                class="block rounded-3xl border border-zinc-200 px-4 py-4 transition hover:border-zinc-300 hover:bg-zinc-50"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="truncate text-base font-semibold text-zinc-950">{{ car.label }}</p>
                        <p class="mt-1 text-xs text-zinc-400">{{ car.year }}<span v-if="car.plate"> · {{ car.plate }}</span></p>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-[11px] font-medium" :class="Number(car.estimated_price) > 0 ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-zinc-100 text-zinc-500 ring-1 ring-zinc-200'">
                        {{ Number(car.estimated_price) > 0 ? 'Con estimado' : 'Sin estimado' }}
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                    <div>
                        <p class="text-xs uppercase tracking-[0.16em] text-zinc-400">Costo total</p>
                        <p class="mt-1 font-semibold text-zinc-900">{{ formatCompactCurrency(car.total_cost) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.16em] text-zinc-400">Estimado</p>
                        <p class="mt-1 font-semibold text-zinc-900">{{ formatCompactCurrency(car.estimated_price) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs uppercase tracking-[0.16em] text-zinc-400">Margen</p>
                        <p class="mt-1 font-semibold" :class="Number(car.potential_profit) >= 0 ? 'text-emerald-600' : 'text-rose-500'">
                            {{ formatCompactCurrency(car.potential_profit) }}
                        </p>
                    </div>
                </div>
            </RouterLink>
        </div>

        <div v-else class="rounded-3xl border border-dashed border-zinc-300 bg-zinc-50 px-6 py-12 text-center text-sm text-zinc-500">
            No hay vehículos disponibles con datos suficientes para destacar oportunidades.
        </div>
    </DashboardPanel>
</template>

<script setup>
import { ScaleIcon } from '@heroicons/vue/24/outline';
import DashboardPanel from './DashboardPanel.vue';
import { formatCompactCurrency } from './formatters';

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});
</script>