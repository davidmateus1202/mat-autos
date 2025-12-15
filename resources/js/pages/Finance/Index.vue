<template>
    <AppContainer>
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Finanzas</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Gestiona tus cuentas y obligaciones financieras</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="showAccountModal = true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 dark:bg-zinc-900 dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-800 transition-colors cursor-pointer hover:opacity-80">
                    <PlusIcon class="h-4 w-4" />
                    <span>Nueva Cuenta</span>
                </button>
                <button @click="showLoanModal = true" class="btn-primary rounded-xl shadow-lg shadow-indigo-500/20 cursor-pointer">
                    <PlusIcon class="h-4 w-4" />
                    <span>Nuevo Préstamo</span>
                </button>
            </div>
        </div>

        <LoadingOverlay :visible="store.loading" />

        <!-- Consolidated Stats -->
        <ConsolidatedStats 
            :total-assets="totalAssets"
            :total-debt="totalDebt"
            :available-credit="availableCredit"
            :net-worth="netWorth"
            :active-loans-count="activeLoansCount"
        />

        <!-- Accounts Grid -->
        <AccountsGrid 
            :accounts="store.accounts"
            :invested-assets="investedAssets"
            @select="openAccountDetails"
        />

        <!-- Loans Section -->
        <LoansList 
            :loans="store.loans"
            @disburse="openDisburseModal"
            @pay="openPaymentModal"
        />

        <!-- Modals -->
        <!-- Create Account Slide-over -->
        <div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <Transition enter-active-class="transition-opacity ease-linear duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity ease-linear duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showAccountModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="showAccountModal = false"></div>
            </Transition>

            <Transition enter-active-class="transform transition ease-in-out duration-500 sm:duration-700" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in-out duration-500 sm:duration-700" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
                <div v-if="showAccountModal" class="fixed inset-y-0 right-0 z-50 w-screen max-w-md bg-white dark:bg-zinc-900 shadow-2xl flex flex-col h-full border-l border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between px-6 py-6 bg-zinc-900 text-white">
                        <h2 class="text-lg font-semibold">Nueva Cuenta Financiera</h2>
                        <button @click="showAccountModal = false" class="text-zinc-400 hover:text-white transition-colors">
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <form @submit.prevent="createAccount" id="account-form" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nombre de la Cuenta</label>
                                <input v-model="accountForm.name" type="text" required placeholder="Ej: Cuenta Principal" class="input" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Banco / Entidad</label>
                                <input v-model="accountForm.bank_name" type="text" required placeholder="Ej: Bancolombia" class="input" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Número de Cuenta</label>
                                <input v-model="accountForm.account_number" type="text" required placeholder="000-000-000" class="input" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tipo</label>
                                <select v-model="accountForm.type" required class="input">
                                    <option value="bank">Cuenta Bancaria</option>
                                    <option value="cash">Efectivo / Caja</option>
                                    <option value="credit_card">Tarjeta de Crédito</option>
                                    <option value="digital">Billetera Digital</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Saldo Inicial</label>
                                <input v-model="accountForm.initial_balance_display" @input="handleAccountBalanceInput" type="text" required class="input" placeholder="$ 0" />
                            </div>
                        </form>
                    </div>
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                        <button type="button" @click="showAccountModal = false" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700">Cancelar</button>
                        <button type="submit" form="account-form" class="btn-primary rounded-xl">Guardar Cuenta</button>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Create Loan Slide-over -->
        <div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <Transition enter-active-class="transition-opacity ease-linear duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity ease-linear duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showLoanModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="showLoanModal = false"></div>
            </Transition>

            <Transition enter-active-class="transform transition ease-in-out duration-500 sm:duration-700" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in-out duration-500 sm:duration-700" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
                <div v-if="showLoanModal" class="fixed inset-y-0 right-0 z-50 w-screen max-w-md bg-white dark:bg-zinc-900 shadow-2xl flex flex-col h-full border-l border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between px-6 py-6 bg-zinc-900 text-white">
                        <h2 class="text-lg font-semibold">Nuevo Préstamo Bancario</h2>
                        <button @click="showLoanModal = false" class="text-zinc-400 hover:text-white transition-colors">
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <form @submit.prevent="createLoan" id="loan-form" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Entidad Bancaria</label>
                                <input v-model="loanForm.bank_name" type="text" required placeholder="Ej: Davivienda" class="input" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Monto del Préstamo</label>
                                <input v-model="loanForm.amount_display" @input="handleLoanAmountInput" type="text" required class="input" placeholder="$ 0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tasa de Interés (%) <span class="text-zinc-400 font-normal">(Opcional)</span></label>
                                <input v-model="loanForm.interest_rate" type="number" step="0.01" class="input" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Fecha de Inicio</label>
                                <input v-model="loanForm.start_date" type="date" required class="input" />
                            </div>
                        </form>
                    </div>
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                        <button type="button" @click="showLoanModal = false" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700">Cancelar</button>
                        <button type="submit" form="loan-form" class="btn-primary rounded-xl">Registrar Préstamo</button>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Disburse Slide-over -->
        <div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <Transition enter-active-class="transition-opacity ease-linear duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity ease-linear duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showDisburseModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="showDisburseModal = false"></div>
            </Transition>

            <Transition enter-active-class="transform transition ease-in-out duration-500 sm:duration-700" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in-out duration-500 sm:duration-700" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
                <div v-if="showDisburseModal" class="fixed inset-y-0 right-0 z-50 w-screen max-w-md bg-white dark:bg-zinc-900 shadow-2xl flex flex-col h-full border-l border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between px-6 py-6 bg-zinc-900 text-white">
                        <h2 class="text-lg font-semibold">Desembolsar Préstamo</h2>
                        <button @click="showDisburseModal = false" class="text-zinc-400 hover:text-white transition-colors">
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <p class="text-sm text-zinc-500 mb-6">Selecciona la cuenta donde se depositará el dinero del préstamo.</p>
                        <form @submit.prevent="disburseLoan" id="disburse-form" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Monto a Desembolsar</label>
                                <input v-model="disburseForm.amount_display" @input="handleDisburseAmountInput" type="text" required class="input" placeholder="$ 0" />
                                <p class="text-xs text-zinc-500 mt-1">Cupo disponible: {{ formatCurrency(selectedLoan?.available_credit || 0) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Cuenta de Destino</label>
                                <select v-model="disburseForm.account_id" required class="input">
                                    <option v-for="acc in store.accounts" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Fecha de Desembolso</label>
                                <input v-model="disburseForm.disbursed_at" type="date" required class="input" />
                            </div>
                        </form>
                    </div>
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                        <button type="button" @click="showDisburseModal = false" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700">Cancelar</button>
                        <button type="submit" form="disburse-form" class="btn-primary rounded-xl">Confirmar Desembolso</button>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Payment Slide-over -->
        <div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <Transition enter-active-class="transition-opacity ease-linear duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity ease-linear duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showPaymentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="showPaymentModal = false"></div>
            </Transition>

            <Transition enter-active-class="transform transition ease-in-out duration-500 sm:duration-700" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in-out duration-500 sm:duration-700" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
                <div v-if="showPaymentModal" class="fixed inset-y-0 right-0 z-50 w-screen max-w-md bg-white dark:bg-zinc-900 shadow-2xl flex flex-col h-full border-l border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between px-6 py-6 bg-zinc-900 text-white">
                        <h2 class="text-lg font-semibold">Pagar Cuota de Préstamo</h2>
                        <button @click="showPaymentModal = false" class="text-zinc-400 hover:text-white transition-colors">
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <form @submit.prevent="payLoan" id="payment-form" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Monto Total a Pagar</label>
                                <input v-model="paymentForm.amount_display" @input="handlePaymentAmountInput" type="text" required class="input" placeholder="$ 0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Intereses (Incluidos en el monto)</label>
                                <input v-model="paymentForm.interest_amount_display" @input="handlePaymentInterestInput" type="text" class="input" placeholder="$ 0" />
                                <p class="text-xs text-zinc-500 mt-1">Capital: {{ formatCurrency((paymentForm.amount || 0) - (paymentForm.interest_amount || 0)) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Fecha de Pago</label>
                                <input v-model="paymentForm.payment_date" type="date" required class="input" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Cuenta de Origen</label>
                                <select v-model="paymentForm.account_id" required class="input">
                                    <option v-for="acc in store.accounts" :key="acc.id" :value="acc.id">{{ acc.name }} ({{ formatCurrency(acc.balance) }})</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                        <button type="button" @click="showPaymentModal = false" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700">Cancelar</button>
                        <button type="submit" form="payment-form" class="btn-primary rounded-xl">Registrar Pago</button>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Account Details Slide-over -->
        <div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <Transition enter-active-class="transition-opacity ease-linear duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity ease-linear duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showAccountDetailsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="showAccountDetailsModal = false"></div>
            </Transition>

            <Transition enter-active-class="transform transition ease-in-out duration-500 sm:duration-700" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in-out duration-500 sm:duration-700" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
                <div v-if="showAccountDetailsModal" class="fixed inset-y-0 right-0 z-50 w-screen max-w-md bg-white dark:bg-zinc-900 shadow-2xl flex flex-col h-full border-l border-zinc-200 dark:border-zinc-800">
                    <!-- Header -->
                    <div class="px-6 py-6 bg-zinc-900 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold">Detalles de Cuenta</h2>
                            <button @click="showAccountDetailsModal = false" class="text-zinc-400 hover:text-white transition-colors">
                                <XMarkIcon class="h-6 w-6" />
                            </button>
                        </div>
                        <div v-if="selectedAccount">
                            <p class="text-zinc-400 text-sm">{{ selectedAccount.bank_name }}</p>
                            <h3 class="text-2xl font-bold mt-1">{{ selectedAccount.name }}</h3>
                            <p class="text-zinc-400 text-sm mt-1 font-mono">{{ selectedAccount.account_number }}</p>
                            <div class="mt-4 flex items-baseline gap-2">
                                <span class="text-3xl font-bold">{{ formatCurrency(selectedAccount.balance) }}</span>
                                <span class="text-sm text-zinc-400">Saldo Disponible</span>
                            </div>
                        </div>
                    </div>

                    <!-- Body: Adjustment Form -->
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        <div class="mb-8">
                            <h4 class="text-sm font-medium text-zinc-900 dark:text-white mb-4">Ajustar Saldo</h4>
                            <form @submit.prevent="submitAdjustment" class="space-y-4">
                                <div class="flex gap-2 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                                    <button type="button" @click="adjustmentForm.type = 'credit'" :class="{'bg-white dark:bg-zinc-700 shadow-sm text-emerald-600 dark:text-emerald-400': adjustmentForm.type === 'credit', 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200': adjustmentForm.type !== 'credit'}" class="flex-1 py-2 text-sm font-medium rounded-md transition-all">
                                        Ingresar Dinero
                                    </button>
                                    <button type="button" @click="adjustmentForm.type = 'debit'" :class="{'bg-white dark:bg-zinc-700 shadow-sm text-red-600 dark:text-red-400': adjustmentForm.type === 'debit', 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200': adjustmentForm.type !== 'debit'}" class="flex-1 py-2 text-sm font-medium rounded-md transition-all">
                                        Retirar Dinero
                                    </button>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Monto</label>
                                    <input v-model="adjustmentForm.amount_display" @input="handleAdjustmentAmountInput" type="text" required class="input" placeholder="$ 0" />
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Descripción</label>
                                    <input v-model="adjustmentForm.description" type="text" required class="input" placeholder="Ej: Ajuste de caja menor" />
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1">Fecha</label>
                                    <input v-model="adjustmentForm.date" type="date" required class="input" />
                                </div>

                                <button type="submit" class="w-full btn-primary justify-center cursor-pointer">
                                    {{ adjustmentForm.type === 'credit' ? 'Registrar Ingreso' : 'Registrar Retiro' }}
                                </button>
                            </form>
                        </div>

                        <!-- History -->
                        <div v-if="accountDetails && accountDetails.movements">
                            <h4 class="text-sm font-medium text-zinc-900 dark:text-white mb-4 sticky top-0 bg-white dark:bg-zinc-900 py-2">Historial Reciente</h4>
                            <div class="space-y-4">
                                <div v-for="movement in accountDetails.movements.slice(0, 10)" :key="movement.id" class="flex items-start gap-3 text-sm">
                                    <div :class="movement.amount >= 0 ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'" class="p-2 rounded-lg shrink-0">
                                        <component :is="movement.amount >= 0 ? ArrowTrendingUpIcon : ArrowTrendingDownIcon" class="h-4 w-4" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-zinc-900 dark:text-white truncate">{{ movement.description }}</p>
                                        <p class="text-xs text-zinc-500">{{ new Date(movement.movement_date).toLocaleDateString() }}</p>
                                    </div>
                                    <span :class="movement.amount >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'" class="font-medium whitespace-nowrap">
                                        {{ movement.amount >= 0 ? '+' : '' }}{{ formatCurrency(movement.amount) }}
                                    </span>
                                </div>
                                <div v-if="accountDetails.movements.length === 0" class="text-center text-zinc-500 text-sm py-4">
                                    No hay movimientos registrados
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>

    </AppContainer>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useFinanceStore } from '../../stores/useFinance';
import { useDashboardsStore } from '../../stores/useDashboards';
import AppContainer from '../../components/ui/AppContainer.vue';
import LoadingOverlay from '../../components/ui/LoadingOverlay.vue';
import ConsolidatedStats from './partials/ConsolidatedStats.vue';
import AccountsGrid from './partials/AccountsGrid.vue';
import LoansList from './partials/LoansList.vue';
import { 
    PlusIcon,
    XMarkIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon
} from '@heroicons/vue/24/outline';

const store = useFinanceStore();
const dashboardStore = useDashboardsStore();

const showAccountModal = ref(false);
const showLoanModal = ref(false);
const showDisburseModal = ref(false);
const showPaymentModal = ref(false);
const showAccountDetailsModal = ref(false);
const selectedLoan = ref(null);
const selectedAccount = ref(null);
const accountDetails = ref(null);

// Computed Properties for Consolidated Stats
const totalCash = computed(() => {
    return store.accounts.reduce((sum, account) => sum + Number(account.balance), 0);
});

const investedAssets = computed(() => {
    return Number(dashboardStore.summary.invested_assets || 0);
});

const totalAssets = computed(() => {
    return totalCash.value + investedAssets.value;
});

const totalDebt = computed(() => {
    return store.loans.reduce((sum, loan) => sum + Number(loan.current_debt), 0);
});

const availableCredit = computed(() => {
    return store.loans.reduce((sum, loan) => sum + Number(loan.available_credit), 0);
});

const netWorth = computed(() => {
    return totalAssets.value - totalDebt.value;
});

const activeLoansCount = computed(() => {
    return store.loans.filter(l => l.current_debt > 0).length;
});

const accountForm = reactive({
    name: '',
    bank_name: '',
    account_number: '',
    type: 'bank',
    initial_balance: 0,
    initial_balance_display: ''
});

const adjustmentForm = reactive({
    type: 'credit', // credit (add) or debit (subtract)
    amount: '',
    amount_display: '',
    description: '',
    date: new Date().toISOString().split('T')[0]
});

const loanForm = reactive({
    bank_name: '',
    amount: '',
    amount_display: '',
    interest_rate: '',
    start_date: new Date().toISOString().split('T')[0]
});

const disburseForm = reactive({
    account_id: '',
    amount: '',
    amount_display: '',
    disbursed_at: new Date().toISOString().split('T')[0]
});

const paymentForm = reactive({
    amount: '',
    amount_display: '',
    interest_amount: 0,
    interest_amount_display: '',
    payment_date: new Date().toISOString().split('T')[0],
    account_id: ''
});

onMounted(() => {
    store.fetchAccounts();
    store.fetchLoans();
    dashboardStore.fetchSummary();
});

const handleCurrencyInput = (event, form, field, displayField) => {
    let value = event.target.value.replace(/[^\d]/g, '');
    form[field] = value ? parseInt(value) : 0;
    
    if (value) {
        form[displayField] = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    } else {
        form[displayField] = '';
    }
};

const handleAccountBalanceInput = (event) => handleCurrencyInput(event, accountForm, 'initial_balance', 'initial_balance_display');
const handleLoanAmountInput = (event) => handleCurrencyInput(event, loanForm, 'amount', 'amount_display');
const handleDisburseAmountInput = (event) => handleCurrencyInput(event, disburseForm, 'amount', 'amount_display');
const handlePaymentAmountInput = (event) => handleCurrencyInput(event, paymentForm, 'amount', 'amount_display');
const handlePaymentInterestInput = (event) => handleCurrencyInput(event, paymentForm, 'interest_amount', 'interest_amount_display');
const handleAdjustmentAmountInput = (event) => handleCurrencyInput(event, adjustmentForm, 'amount', 'amount_display');

const openAccountDetails = async (account) => {
    selectedAccount.value = account;
    showAccountDetailsModal.value = true;
    accountDetails.value = null; // Reset while loading
    const details = await store.fetchAccountDetails(account.id);
    if (details) {
        accountDetails.value = details;
        // Sort movements by date desc
        if (accountDetails.value.movements) {
            accountDetails.value.movements.sort((a, b) => new Date(b.movement_date) - new Date(a.movement_date));
        }
    }
};

const submitAdjustment = async () => {
    if (!selectedAccount.value) return;
    console.log('Submitting adjustment for account:', selectedAccount.value.id);
    
    const amount = adjustmentForm.type === 'debit' ? -Math.abs(adjustmentForm.amount) : Math.abs(adjustmentForm.amount);
    
    const success = await store.adjustAccountBalance(selectedAccount.value.id, {
        amount: amount,
        description: adjustmentForm.description,
        date: adjustmentForm.date
    });
    
    if (success) {
        // Refresh details
        const details = await store.fetchAccountDetails(selectedAccount.value.id);
        if (details) {
            accountDetails.value = details;
            if (accountDetails.value.movements) {
                accountDetails.value.movements.sort((a, b) => new Date(b.movement_date) - new Date(a.movement_date));
            }
            // Update selected account balance in the view immediately if possible, 
            // but fetchAccountDetails returns the updated account, so we can update selectedAccount too
            selectedAccount.value = details; 
        }
        // Reset form
        adjustmentForm.amount = '';
        adjustmentForm.amount_display = '';
        adjustmentForm.description = '';
        adjustmentForm.date = new Date().toISOString().split('T')[0];
    }
};

const createAccount = async () => {
    const success = await store.createAccount(accountForm);
    if (success) {
        showAccountModal.value = false;
        Object.assign(accountForm, { name: '', bank_name: '', account_number: '', type: 'bank', initial_balance: 0, initial_balance_display: '' });
    }
};

const createLoan = async () => {
    const success = await store.createLoan(loanForm);
    if (success) {
        showLoanModal.value = false;
        Object.assign(loanForm, { bank_name: '', amount: '', amount_display: '', interest_rate: '', start_date: new Date().toISOString().split('T')[0] });
    }
};

const openDisburseModal = (loan) => {
    selectedLoan.value = loan;
    // Pre-select cash account if available, otherwise first account
    const cashAccount = store.accounts.find(a => a.type === 'cash');
    if (cashAccount) {
        disburseForm.account_id = cashAccount.id;
    } else if (store.accounts.length > 0) {
        disburseForm.account_id = store.accounts[0].id;
    }
    
    // Initialize amount with available credit
    disburseForm.amount = loan.available_credit;
    disburseForm.amount_display = formatCurrency(loan.available_credit);
    
    showDisburseModal.value = true;
};

const disburseLoan = async () => {
    if (!selectedLoan.value) return;
    const success = await store.disburseLoan(selectedLoan.value.id, disburseForm);
    if (success) {
        showDisburseModal.value = false;
        selectedLoan.value = null;
    }
};

const openPaymentModal = (loan) => {
    selectedLoan.value = loan;
    showPaymentModal.value = true;
};

const payLoan = async () => {
    if (!selectedLoan.value) return;
    
    const payload = {
        ...paymentForm,
        principal_amount: paymentForm.amount - paymentForm.interest_amount,
        interest_amount: paymentForm.interest_amount
    };

    const success = await store.payLoan(selectedLoan.value.id, payload);
    if (success) {
        showPaymentModal.value = false;
        selectedLoan.value = null;
        Object.assign(paymentForm, { 
            amount: '', 
            amount_display: '', 
            interest_amount: 0, 
            interest_amount_display: '', 
            payment_date: new Date().toISOString().split('T')[0], 
            account_id: '' 
        });
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(value);
};
</script>
