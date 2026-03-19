<template>
    <AppContainer class="min-h-full">
        <div v-if="store.loading" class="py-8">
            <SkeletonRows :rows="6" />
        </div>

        <div v-else class="space-y-6">
            <DashboardHero
                :highlights="heroHighlights"
                :stats="heroStats"
                :current-month-label="currentMonthLabel"
                :net-profit-month="summary.net_profit_month"
                :current-debt="summary.current_debt"
                :potential-inventory-profit="summary.potential_inventory_profit"
            />

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <DashboardMetricCard v-for="card in metricCards" :key="card.key" v-bind="card" />
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.55fr,0.95fr,0.95fr]">
                <DashboardMonthlyPerformance :items="monthlySeries" :latest-month="latestMonth" />
                <DashboardBrandDistribution :brands="store.salesByBrand" />
                <DashboardPortfolioStatus
                    :items="portfolioStatuses"
                    :potential-inventory-profit="summary.potential_inventory_profit"
                />
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.15fr,0.95fr,1.05fr]">
                <DashboardInventorySpotlight :items="spotlightInventory" />

                <DashboardActivitySection
                    eyebrow="Actividad"
                    title="Ventas recientes"
                    description="Últimos carros vendidos y margen neto capturado."
                    :icon="ArrowTrendingUpIcon"
                    :items="recentSales"
                    empty-text="Todavía no hay ventas recientes para mostrar."
                >
                    <template #item="{ item }">
                        <RouterLink
                            :to="{ name: 'cars.show', params: { id: item.id } }"
                            class="flex items-center gap-3 rounded-[22px] bg-zinc-50 px-4 py-3 transition hover:bg-zinc-100"
                        >
                            <div class="rounded-2xl bg-emerald-50 p-3 text-emerald-600 ring-1 ring-emerald-100">
                                <ArrowTrendingUpIcon class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-zinc-950">{{ item.label }}</p>
                                <p class="mt-1 text-xs text-zinc-400">{{ formatShortDate(item.sold_at) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-zinc-950">{{ formatCompactCurrency(item.sale_price) }}</p>
                                <p class="mt-1 text-xs font-medium" :class="Number(item.net_profit) >= 0 ? 'text-emerald-600' : 'text-rose-500'">
                                    {{ formatCompactCurrency(item.net_profit) }}
                                </p>
                            </div>
                        </RouterLink>
                    </template>
                </DashboardActivitySection>

                <DashboardActivitySection
                    eyebrow="Control de costos"
                    title="Gastos recientes"
                    description="Últimos egresos registrados sobre el inventario."
                    :icon="WrenchScrewdriverIcon"
                    :items="recentExpenses"
                    empty-text="Todavía no hay gastos recientes para mostrar."
                >
                    <template #item="{ item }">
                        <RouterLink
                            :to="{ name: 'cars.index' }"
                            class="flex items-center gap-3 rounded-[22px] bg-zinc-50 px-4 py-3 transition hover:bg-zinc-100"
                        >
                            <div class="rounded-2xl bg-amber-50 p-3 text-amber-600 ring-1 ring-amber-100">
                                <WrenchScrewdriverIcon class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-zinc-950">{{ item.concept }}</p>
                                <p class="mt-1 truncate text-xs text-zinc-400">{{ item.car_label }} · {{ formatShortDate(item.expense_date) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-zinc-950">{{ formatCompactCurrency(item.amount) }}</p>
                                <p class="mt-1 text-xs text-zinc-400">Egreso</p>
                            </div>
                        </RouterLink>
                    </template>
                </DashboardActivitySection>
            </section>
        </div>
    </AppContainer>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import {
    ArrowTrendingDownIcon,
    ArrowTrendingUpIcon,
    BanknotesIcon,
    CreditCardIcon,
    TruckIcon,
    WrenchScrewdriverIcon,
} from '@heroicons/vue/24/outline';
import AppContainer from '../../components/ui/AppContainer.vue';
import SkeletonRows from '../../components/ui/SkeletonRows.vue';
import { useDashboardsStore } from '../../stores/useDashboards';
import DashboardActivitySection from './partials/DashboardActivitySection.vue';
import DashboardBrandDistribution from './partials/DashboardBrandDistribution.vue';
import DashboardHero from './partials/DashboardHero.vue';
import DashboardInventorySpotlight from './partials/DashboardInventorySpotlight.vue';
import DashboardMetricCard from './partials/DashboardMetricCard.vue';
import DashboardMonthlyPerformance from './partials/DashboardMonthlyPerformance.vue';
import DashboardPortfolioStatus from './partials/DashboardPortfolioStatus.vue';
import { formatCompactCurrency, formatCurrency, formatShortDate } from './partials/formatters';

const store = useDashboardsStore();

onMounted(() => {
    store.fetchAll();
});

const summary = computed(() => store.summary);

const currentMonthLabel = computed(() => {
    return new Intl.DateTimeFormat('es-CO', { month: 'long', year: 'numeric' }).format(new Date());
});

const heroHighlights = computed(() => [
    {
        label: 'Ingresos del mes',
        value: formatCompactCurrency(summary.value.sales_revenue_month),
        note: `${summary.value.cars_sold_month} ventas cerradas`,
    },
    {
        label: 'Gastos del mes',
        value: formatCompactCurrency(summary.value.expenses_month),
        note: `Salidas ${formatCompactCurrency(summary.value.outflows_month)}`,
    },
    {
        label: 'Ticket promedio',
        value: formatCompactCurrency(summary.value.average_sale_ticket_month),
        note: 'Promedio por carro vendido',
    },
]);

const heroStats = computed(() => [
    { label: 'Disponibles', value: summary.value.available_cars },
    { label: 'Vendidos', value: summary.value.sold_cars_total },
    { label: 'Apartados', value: summary.value.reserved_cars },
    { label: 'Préstamos', value: summary.value.active_loans },
]);

const metricCards = computed(() => {
    const netProfit = Number(summary.value.net_profit_month || 0);

    return [
        {
            key: 'cash',
            title: 'Caja disponible',
            value: formatCurrency(summary.value.available_cash),
            helper: `Entradas del mes ${formatCompactCurrency(summary.value.inflows_month)}`,
            subtext: `Salidas ${formatCompactCurrency(summary.value.outflows_month)}`,
            badge: 'Liquidez',
            icon: BanknotesIcon,
            toneClass: 'bg-emerald-50 text-emerald-600 ring-emerald-100',
        },
        {
            key: 'inventory',
            title: 'Capital en inventario',
            value: formatCurrency(summary.value.invested_assets),
            helper: `${summary.value.available_cars} vehículos disponibles`,
            subtext: `Valor estimado ${formatCompactCurrency(summary.value.estimated_inventory_value)}`,
            badge: 'Stock',
            icon: TruckIcon,
            toneClass: 'bg-sky-50 text-sky-600 ring-sky-100',
        },
        {
            key: 'profit',
            title: 'Utilidad neta del mes',
            value: formatCurrency(summary.value.net_profit_month),
            helper: `Bruta ${formatCompactCurrency(summary.value.gross_profit_month)}`,
            subtext: `${summary.value.cars_sold_month} carros vendidos`,
            badge: 'Rentabilidad',
            icon: netProfit >= 0 ? ArrowTrendingUpIcon : ArrowTrendingDownIcon,
            toneClass: netProfit >= 0 ? 'bg-emerald-50 text-emerald-600 ring-emerald-100' : 'bg-rose-50 text-rose-600 ring-rose-100',
        },
        {
            key: 'credit',
            title: 'Crédito utilizable',
            value: formatCurrency(summary.value.available_credit),
            helper: `Deuda actual ${formatCompactCurrency(summary.value.current_debt)}`,
            subtext: `${summary.value.active_loans} préstamos con saldo`,
            badge: 'Financiación',
            icon: CreditCardIcon,
            toneClass: 'bg-amber-50 text-amber-600 ring-amber-100',
        },
    ];
});

const monthlySeries = computed(() => {
    return Array.isArray(store.monthlyStats) ? store.monthlyStats : [];
});

const latestMonth = computed(() => {
    return monthlySeries.value.at(-1) ?? {
        sales_amount: 0,
        expenses_amount: 0,
        net_profit: 0,
    };
});

const statusToneMap = {
    available: 'bg-sky-500',
    sold: 'bg-emerald-500',
    reserved: 'bg-amber-500',
};

const portfolioStatuses = computed(() => [
    ...(Array.isArray(summary.value.status_breakdown)
        ? summary.value.status_breakdown.map((item) => ({
            label: item.label,
            value: item.count,
            dotClass: statusToneMap[item.key] || 'bg-zinc-400',
        }))
        : []),
    {
        label: 'Valor estimado del stock',
        value: formatCompactCurrency(summary.value.estimated_inventory_value),
        dotClass: 'bg-indigo-500',
    },
]);

const spotlightInventory = computed(() => {
    return Array.isArray(summary.value.top_inventory) ? summary.value.top_inventory : [];
});

const recentSales = computed(() => {
    return Array.isArray(summary.value.recent_sales) ? summary.value.recent_sales : [];
});

const recentExpenses = computed(() => {
    return Array.isArray(summary.value.recent_expenses) ? summary.value.recent_expenses : [];
});
</script>
