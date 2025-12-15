<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Consolidated Assets -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <ScaleIcon class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Patrimonio Consolidado</p>
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ formatCurrency(netWorth) }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between text-sm">
                <span class="text-zinc-500">Activos Totales</span>
                <span class="font-medium text-zinc-900 dark:text-white">{{ formatCurrency(totalAssets) }}</span>
            </div>
        </div>

        <!-- Total Debt -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400">
                    <BanknotesIcon class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Deuda Total</p>
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ formatCurrency(totalDebt) }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between text-sm">
                <span class="text-zinc-500">Préstamos Activos</span>
                <span class="font-medium text-zinc-900 dark:text-white">{{ activeLoansCount }}</span>
            </div>
        </div>

        <!-- Available Credit -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <CreditCardIcon class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Cupo Disponible</p>
                    <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ formatCurrency(availableCredit) }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between text-sm">
                <span class="text-zinc-500">Capacidad de Endeudamiento</span>
                <span class="font-medium text-emerald-600 dark:text-emerald-400">Disponible</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ScaleIcon, BanknotesIcon, CreditCardIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    totalAssets: { type: Number, default: 0 },
    totalDebt: { type: Number, default: 0 },
    availableCredit: { type: Number, default: 0 },
    netWorth: { type: Number, default: 0 },
    activeLoansCount: { type: Number, default: 0 }
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(value);
};
</script>