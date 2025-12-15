<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        <!-- Investment Card (Static) -->
        <div class="group relative bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-800 hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="absolute -right-6 -top-6 h-32 w-32 rounded-full bg-emerald-50 dark:bg-emerald-900/20 group-hover:scale-110 transition-transform duration-500 ease-out"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-6">
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-500/20 transition-colors">
                        <TruckIcon class="h-6 w-6" />
                    </div>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                        Activo
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-1">Inversión en Vehículos</p>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 truncate">Inventario Total</h3>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">{{ formatCurrency(investedAssets) }}</span>
                    </div>
                    <p class="text-xs text-zinc-400 mt-2 font-mono">Capital en inventario</p>
                </div>
            </div>
        </div>

        <!-- Dynamic Accounts -->
        <div v-for="account in accounts" :key="account.id" @click="$emit('select', account)" class="cursor-pointer group relative bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-800 hover:shadow-md transition-all duration-300 overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute -right-6 -top-6 h-32 w-32 rounded-full bg-zinc-50 dark:bg-zinc-800/50 group-hover:scale-110 transition-transform duration-500 ease-out"></div>
            
            <div class="relative">
                <div class="flex items-start justify-between mb-6">
                    <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-500/20 transition-colors">
                        <component :is="getAccountIcon(account.type)" class="h-6 w-6" />
                    </div>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300">
                        {{ getAccountTypeLabel(account.type) }}
                    </span>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mb-1">{{ account.bank_name }}</p>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 truncate" :title="account.name">{{ account.name }}</h3>
                    
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">{{ formatCurrency(account.balance) }}</span>
                    </div>
                    <p class="text-xs text-zinc-400 mt-2 font-mono">{{ account.account_number }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { 
    BuildingLibraryIcon, 
    BanknotesIcon, 
    CreditCardIcon, 
    WalletIcon, 
    TruckIcon
} from '@heroicons/vue/24/outline';

defineProps({
    accounts: { type: Array, default: () => [] },
    investedAssets: { type: Number, default: 0 }
});

defineEmits(['select']);

const getAccountIcon = (type) => {
    switch (type) {
        case 'bank': return BuildingLibraryIcon;
        case 'cash': return BanknotesIcon;
        case 'credit_card': return CreditCardIcon;
        case 'digital': return WalletIcon;
        default: return BuildingLibraryIcon;
    }
};

const getAccountTypeLabel = (type) => {
    switch (type) {
        case 'bank': return 'Bancaria';
        case 'cash': return 'Efectivo';
        case 'credit_card': return 'Crédito';
        case 'digital': return 'Digital';
        default: return type;
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(value);
};
</script>