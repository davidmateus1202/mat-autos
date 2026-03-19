<template>
    <DashboardPanel
        eyebrow="Distribución"
        title="Ventas por marca"
        description="Participación de cada marca dentro de los carros vendidos."
        :icon="ChartPieIcon"
    >
        <div v-if="segments.length" class="grid gap-6 md:grid-cols-[160px,1fr] md:items-center">
            <div class="relative mx-auto h-40 w-40 rounded-full" :style="donutStyle">
                <div class="absolute inset-5 rounded-full bg-white shadow-inner"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-3xl font-semibold tracking-tight text-zinc-950">{{ totalSales }}</p>
                        <p class="text-[11px] uppercase tracking-[0.2em] text-zinc-400">vendidos</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <div
                    v-for="brand in segments"
                    :key="brand.name"
                    class="flex items-center justify-between rounded-2xl bg-zinc-50 px-4 py-3"
                >
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="h-3 w-3 shrink-0 rounded-full" :style="{ backgroundColor: brand.color }"></span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900">{{ brand.name }}</p>
                            <p class="text-xs text-zinc-400">{{ formatCompactCurrency(brand.revenue) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-zinc-900">{{ brand.share.toFixed(1) }}%</p>
                        <p class="text-xs text-zinc-400">{{ brand.total }} ventas</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="rounded-3xl border border-dashed border-zinc-300 bg-zinc-50 px-6 py-12 text-center text-sm text-zinc-500">
            Aún no hay ventas registradas para segmentar por marca.
        </div>
    </DashboardPanel>
</template>

<script setup>
import { computed } from 'vue';
import { ChartPieIcon } from '@heroicons/vue/24/outline';
import DashboardPanel from './DashboardPanel.vue';
import { formatCompactCurrency } from './formatters';

const props = defineProps({
    brands: {
        type: Array,
        default: () => [],
    },
});

const segments = computed(() => {
    const palette = ['#2563eb', '#60a5fa', '#818cf8', '#22c55e'];
    const baseBrands = Array.isArray(props.brands)
        ? props.brands
            .map((brand) => ({
                name: brand.name,
                total: Number(brand.total || 0),
                revenue: Number(brand.revenue || 0),
                net_profit: Number(brand.net_profit || 0),
            }))
            .filter((brand) => brand.total > 0)
        : [];

    if (!baseBrands.length) {
        return [];
    }

    const primaryBrands = baseBrands.slice(0, 3);
    const others = baseBrands.slice(3).reduce(
        (carry, brand) => ({
            name: 'Otras',
            total: carry.total + brand.total,
            revenue: carry.revenue + brand.revenue,
            net_profit: carry.net_profit + brand.net_profit,
        }),
        { name: 'Otras', total: 0, revenue: 0, net_profit: 0 }
    );

    const mergedBrands = others.total > 0 ? [...primaryBrands, others] : primaryBrands;
    const total = mergedBrands.reduce((sum, brand) => sum + brand.total, 0) || 1;

    return mergedBrands.map((brand, index) => ({
        ...brand,
        color: palette[index % palette.length],
        share: (brand.total / total) * 100,
    }));
});

const totalSales = computed(() => segments.value.reduce((sum, brand) => sum + brand.total, 0));

const donutStyle = computed(() => {
    if (!segments.value.length) {
        return { background: '#e4e4e7' };
    }

    let angle = 0;
    const gradientSegments = segments.value.map((brand) => {
        const start = angle;
        const sweep = (brand.share / 100) * 360;
        angle += sweep;

        return `${brand.color} ${start}deg ${angle}deg`;
    });

    return {
        background: `conic-gradient(${gradientSegments.join(', ')})`,
    };
});
</script>