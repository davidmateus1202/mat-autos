<template>
    <DashboardPanel
        eyebrow="Performance"
        title="Rendimiento mensual"
        description="Volumen de ventas y rentabilidad neta para seguir la tracción comercial del inventario."
        headerClass="flex flex-col gap-4 md:flex-row md:items-start md:justify-between"
        bodyClass="mt-8"
    >
        <template #headerAction>
            <div class="grid grid-cols-3 gap-2 rounded-2xl bg-zinc-50 p-2 text-center">
                <div class="rounded-2xl bg-white px-3 py-2 ring-1 ring-zinc-200/80">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-zinc-400">Ventas</p>
                    <p class="mt-1 text-sm font-semibold text-zinc-950">{{ formatCompactCurrency(latestSummary.sales_amount) }}</p>
                </div>
                <div class="rounded-2xl bg-white px-3 py-2 ring-1 ring-zinc-200/80">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-zinc-400">Gastos</p>
                    <p class="mt-1 text-sm font-semibold text-zinc-950">{{ formatCompactCurrency(latestSummary.expenses_amount) }}</p>
                </div>
                <div class="rounded-2xl bg-white px-3 py-2 ring-1 ring-zinc-200/80">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-zinc-400">Utilidad</p>
                    <p class="mt-1 text-sm font-semibold" :class="Number(latestSummary.net_profit) >= 0 ? 'text-emerald-600' : 'text-rose-500'">
                        {{ formatCompactCurrency(latestSummary.net_profit) }}
                    </p>
                </div>
            </div>
        </template>

        <div v-if="safeItems.length" class="grid grid-cols-4 gap-3 sm:grid-cols-8">
            <div v-for="stat in safeItems" :key="stat.month" class="flex flex-col items-center gap-3">
                <div class="flex h-44 items-end justify-center">
                    <div class="flex h-full w-11 items-end rounded-full bg-zinc-100 p-1.5">
                        <div
                            class="w-full rounded-full bg-linear-to-t from-zinc-900 to-sky-500"
                            :style="{ height: `${barHeight(stat)}%` }"
                        ></div>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">{{ stat.label }}</p>
                    <p class="mt-1 text-[11px] text-zinc-400">{{ stat.sales_count }} ventas</p>
                    <p class="mt-1 text-[11px] font-medium" :class="Number(stat.net_profit) >= 0 ? 'text-emerald-600' : 'text-rose-500'">
                        {{ formatCompactCurrency(stat.net_profit) }}
                    </p>
                </div>
            </div>
        </div>

        <div v-else class="rounded-3xl border border-dashed border-zinc-300 bg-zinc-50 px-6 py-12 text-center text-sm text-zinc-500">
            Aún no hay suficiente historial de ventas para dibujar la tendencia mensual.
        </div>
    </DashboardPanel>
</template>

<script setup>
import { computed } from 'vue';
import DashboardPanel from './DashboardPanel.vue';
import { formatCompactCurrency } from './formatters';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    latestMonth: {
        type: Object,
        default: () => ({
            sales_amount: 0,
            expenses_amount: 0,
            net_profit: 0,
        }),
    },
});

const safeItems = computed(() => (Array.isArray(props.items) ? props.items : []));

const latestSummary = computed(() => ({
    sales_amount: Number(props.latestMonth?.sales_amount || 0),
    expenses_amount: Number(props.latestMonth?.expenses_amount || 0),
    net_profit: Number(props.latestMonth?.net_profit || 0),
}));

const maxSalesAmount = computed(() => {
    const values = safeItems.value.map((stat) => Number(stat.sales_amount || 0));
    return Math.max(1, ...values);
});

function barHeight(stat) {
    const ratio = Number(stat.sales_amount || 0) / maxSalesAmount.value;
    return Math.max(ratio * 100, Number(stat.sales_amount || 0) > 0 ? 12 : 8);
}
</script>