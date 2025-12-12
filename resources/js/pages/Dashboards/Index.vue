<template>
    <AppContainer>
        <PageHeader title="Dashboard" />

        <div v-if="store.loading" class="py-8">
            <SkeletonRows :count="3" />
        </div>

        <div v-else class="space-y-6">
            <!-- Summary Tiles -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatTile title="Available Cash" :value="formatCurrency(store.summary.available_cash)" />
                <StatTile title="Invested Assets" :value="formatCurrency(store.summary.invested_assets)" />
                <StatTile title="Cars Sold (Month)" :value="store.summary.cars_sold_month" />
                <StatTile title="Net Profit (Month)" :value="formatCurrency(store.summary.net_profit_month)" 
                    :trend="store.summary.net_profit_month > 0 ? 'up' : 'down'" />
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Sales by Brand -->
                <div class="card p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Sales by Brand</h3>
                    <div class="h-64 flex items-center justify-center" v-if="store.salesByBrand.length === 0">
                        <p class="text-gray-500">No sales data available</p>
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="brand in store.salesByBrand" :key="brand.name" class="flex items-center">
                            <span class="w-24 text-sm text-gray-600 dark:text-gray-400">{{ brand.name }}</span>
                            <div class="flex-1 h-4 bg-gray-100 rounded-full overflow-hidden dark:bg-zinc-800">
                                <div class="h-full bg-indigo-500" :style="{ width: `${(brand.total / maxBrandSales) * 100}%` }"></div>
                            </div>
                            <span class="ml-4 text-sm font-medium text-gray-900 dark:text-white">{{ brand.total }}</span>
                        </div>
                    </div>
                </div>

                <!-- Monthly Stats -->
                <div class="card p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Monthly Performance</h3>
                    <div class="h-64 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                <tr v-for="stat in store.monthlyStats" :key="stat.label">
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ stat.label }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ stat.sales_count }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right font-medium" 
                                        :class="stat.net_profit >= 0 ? 'text-green-600' : 'text-red-600'">
                                        {{ formatCurrency(stat.net_profit) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppContainer>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useDashboardsStore } from '../../stores/useDashboards';
import AppContainer from '../../components/ui/AppContainer.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import StatTile from '../../components/ui/StatTile.vue';
import SkeletonRows from '../../components/ui/SkeletonRows.vue';

const store = useDashboardsStore();

onMounted(() => {
    store.fetchAll();
});

const maxBrandSales = computed(() => {
    if (!store.salesByBrand.length) return 1;
    return Math.max(...store.salesByBrand.map(b => b.total));
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(value);
};
</script>
