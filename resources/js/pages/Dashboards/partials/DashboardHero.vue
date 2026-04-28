<template>
    <section class="relative overflow-hidden rounded-4xl bg-white p-6 shadow-[0_24px_60px_-36px_rgba(15,23,42,0.32)] ring-1 ring-zinc-200/80 md:p-8">
        <div class="absolute -left-12 bottom-0 h-36 w-36 rounded-full bg-emerald-100/90 blur-3xl"></div>
        <div class="absolute -right-6 top-0 h-44 w-44 rounded-full bg-sky-100/90 blur-3xl"></div>

        <div class="relative grid gap-6 xl:grid-cols-[1.6fr,0.95fr]">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-2 rounded-full bg-sky-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-sky-700 ring-1 ring-sky-100">
                    <SparklesIcon class="h-4 w-4" />
                    Dashboard Ejecutivo
                </div>

                <div class="mt-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-semibold tracking-tight text-zinc-950 md:text-4xl">Panorama del negocio</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-500">
                            Seguimiento de caja, inventario, ventas y rentabilidad para decidir mejor qué comprar,
                            cuándo vender y dónde ajustar gastos.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 rounded-full bg-zinc-100 px-3 py-2 text-xs font-medium text-zinc-600">
                        <ClockIcon class="h-4 w-4" />
                        Últimos 8 meses
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <RouterLink
                        :to="{ name: 'cars.index' }"
                        class="inline-flex items-center gap-2 rounded-full bg-zinc-950 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800"
                    >
                        <TruckIcon class="h-4 w-4" />
                        Ver inventario
                    </RouterLink>
                    <RouterLink
                        :to="{ name: 'finance.index' }"
                        class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition hover:border-zinc-300 hover:bg-zinc-50"
                    >
                        <CreditCardIcon class="h-4 w-4" />
                        Ir a finanzas
                    </RouterLink>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <div
                        v-for="item in highlights"
                        :key="item.label"
                        class="rounded-3xl border border-zinc-200/80 bg-zinc-50/90 px-4 py-4"
                    >
                        <p class="text-xs uppercase tracking-[0.18em] text-zinc-400">{{ item.label }}</p>
                        <p class="mt-2 text-xl font-semibold tracking-tight text-zinc-950">{{ item.value }}</p>
                        <p class="mt-1 text-xs text-zinc-500">{{ item.note }}</p>
                    </div>
                </div>
            </div>

            <div class="min-w-0 grid gap-4 rounded-3xl bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-white/55">Cierre del mes</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight">{{ formatCurrency(netProfitValue) }}</h2>
                        <p class="mt-2 text-sm text-white/70">Utilidad neta registrada en {{ currentMonthLabel }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-3 text-white">
                        <component :is="netProfitValue >= 0 ? ArrowTrendingUpIcon : ArrowTrendingDownIcon" class="h-6 w-6" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div
                        v-for="item in stats"
                        :key="item.label"
                        class="rounded-2xl bg-white/5 px-4 py-4"
                    >
                        <p class="text-xs uppercase tracking-[0.18em] text-white/45">{{ item.label }}</p>
                        <p class="mt-2 text-2xl font-semibold tracking-tight">{{ item.value }}</p>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/5 px-4 py-4">
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3 text-sm">
                        <span class="text-white/60">Deuda actual</span>
                        <span class="font-medium text-white">{{ formatCompactCurrency(currentDebt) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 pt-3 text-sm">
                        <span class="text-white/60">Potencial del inventario</span>
                        <span class="font-medium text-emerald-300">{{ formatCompactCurrency(potentialInventoryProfit) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import {
    ArrowTrendingDownIcon,
    ArrowTrendingUpIcon,
    ClockIcon,
    CreditCardIcon,
    SparklesIcon,
    TruckIcon,
} from '@heroicons/vue/24/outline';
import { formatCompactCurrency, formatCurrency } from './formatters';

const props = defineProps({
    highlights: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Array,
        default: () => [],
    },
    currentMonthLabel: {
        type: String,
        default: '',
    },
    netProfitMonth: {
        type: Number,
        default: 0,
    },
    currentDebt: {
        type: Number,
        default: 0,
    },
    potentialInventoryProfit: {
        type: Number,
        default: 0,
    },
});

const netProfitValue = computed(() => Number(props.netProfitMonth || 0));
</script>