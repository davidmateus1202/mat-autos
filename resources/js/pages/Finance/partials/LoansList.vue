<template>
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Préstamos Bancarios</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Seguimiento de créditos activos</p>
            </div>
        </div>
        
        <div class="hidden md:block rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <table class="w-full text-left text-xs lg:text-sm table-fixed">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 dark:text-zinc-400">
                    <tr>
                        <th class="w-[25%] px-3 lg:px-6 py-4 font-medium truncate">Entidad Bancaria</th>
                        <th class="w-[15%] px-3 lg:px-6 py-4 font-medium text-right truncate">Cupo Total</th>
                        <th class="w-[20%] px-3 lg:px-6 py-4 font-medium text-right truncate">Deuda Actual</th>
                        <th class="w-[15%] px-3 lg:px-6 py-4 font-medium text-right truncate">Disponible</th>
                        <th class="w-[10%] px-3 lg:px-6 py-4 font-medium text-center truncate">Estado</th>
                        <th class="w-[15%] px-3 lg:px-6 py-4 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    <tr v-if="loans.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                            No hay préstamos registrados
                        </td>
                    </tr>
                    <tr v-for="loan in loans" :key="loan.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-3 lg:px-6 py-4 truncate">
                            <div class="flex items-center gap-2 lg:gap-3">
                                <div class="h-8 w-8 lg:h-10 lg:w-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400 shrink-0">
                                    <BuildingLibraryIcon class="h-4 w-4 lg:h-5 lg:w-5" />
                                </div>
                                <div class="min-w-0 overflow-hidden">
                                    <p class="font-medium text-zinc-900 dark:text-white truncate" :title="loan.bank_name">{{ loan.bank_name }}</p>
                                    <p class="text-xs text-zinc-500 truncate">Tasa: {{ loan.interest_rate }}%</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 lg:px-6 py-4 text-right font-medium text-zinc-900 dark:text-white truncate">
                            {{ formatCurrency(loan.amount) }}
                        </td>
                        <td class="px-3 lg:px-6 py-4 text-right">
                            <div class="flex flex-col items-end w-full">
                                <span class="font-bold text-zinc-900 dark:text-white truncate w-full">{{ formatCurrency(loan.current_debt) }}</span>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 mt-2">
                                    <div class="bg-indigo-600 h-1.5 rounded-full" :style="{ width: `${Math.min((loan.current_debt / loan.amount) * 100, 100)}%` }"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 lg:px-6 py-4 text-right font-medium text-emerald-600 dark:text-emerald-400 truncate">
                            {{ formatCurrency(loan.available_credit) }}
                        </td>
                        <td class="px-3 lg:px-6 py-4 text-center">
                            <span :class="getLoanStatusClass(loan.status)" class="inline-flex items-center rounded-full px-2 lg:px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset truncate max-w-full justify-center">
                                {{ getLoanStatusLabel(loan.status) }}
                            </span>
                        </td>
                        <td class="px-3 lg:px-6 py-4 text-right">
                            <div class="flex flex-col xl:flex-row items-end justify-end gap-2">
                                <button v-if="loan.available_credit > 0" @click="$emit('disburse', loan)" class="w-full xl:w-auto text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-xs bg-indigo-50 dark:bg-indigo-900/20 px-2 lg:px-3 py-1.5 rounded-lg transition-colors truncate">
                                    Desembolsar
                                </button>
                                <button v-if="loan.current_debt > 0" @click="$emit('pay', loan)" class="w-full xl:w-auto text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 font-medium text-xs bg-emerald-50 dark:bg-emerald-900/20 px-2 lg:px-3 py-1.5 rounded-lg transition-colors truncate">
                                    Pagar
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-zinc-200 dark:divide-zinc-800">
            <div v-if="loans.length === 0" class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                No hay préstamos registrados
            </div>
            <div v-for="loan in loans" :key="loan.id" class="p-4 space-y-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                            <BuildingLibraryIcon class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-white">{{ loan.bank_name }}</p>
                            <p class="text-xs text-zinc-500">Tasa: {{ loan.interest_rate }}%</p>
                        </div>
                    </div>
                    <span :class="getLoanStatusClass(loan.status)" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset">
                        {{ getLoanStatusLabel(loan.status) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs mb-1">Cupo Total</p>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ formatCurrency(loan.amount) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs mb-1">Deuda Actual</p>
                        <p class="font-bold text-zinc-900 dark:text-white">{{ formatCurrency(loan.current_debt) }}</p>
                    </div>
                    <div class="col-span-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex justify-between items-center">
                            <p class="text-zinc-500 dark:text-zinc-400 text-xs">Disponible</p>
                            <p class="font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(loan.available_credit) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5">
                    <div class="bg-indigo-600 h-1.5 rounded-full" :style="{ width: `${(loan.current_debt / loan.amount) * 100}%` }"></div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button v-if="loan.available_credit > 0" @click="$emit('disburse', loan)" class="w-full text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm bg-indigo-50 dark:bg-indigo-900/20 px-4 py-2 rounded-lg transition-colors">
                        Desembolsar
                    </button>
                    <button v-if="loan.current_debt > 0" @click="$emit('pay', loan)" class="w-full text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 font-medium text-sm bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 rounded-lg transition-colors">
                        Pagar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { BuildingLibraryIcon } from '@heroicons/vue/24/outline';

defineProps({
    loans: { type: Array, default: () => [] }
});

defineEmits(['disburse', 'pay']);

const getLoanStatusClass = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 ring-yellow-600/20';
        case 'approved': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 ring-blue-600/20';
        case 'disbursed': return 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 ring-purple-600/20';
        case 'paid': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 ring-emerald-600/20';
        default: return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300 ring-zinc-600/20';
    }
};

const getLoanStatusLabel = (status) => {
    switch (status) {
        case 'pending': return 'Pendiente';
        case 'approved': return 'Aprobado';
        case 'disbursed': return 'Desembolsado';
        case 'paid': return 'Pagado';
        default: return status;
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(value);
};
</script>