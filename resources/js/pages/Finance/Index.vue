<template>
    <AppContainer>
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Finanzas</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Gestiona tus cuentas y obligaciones financieras</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="showAccountModal = true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 dark:bg-zinc-900 dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-800 transition-colors">
                    <PlusIcon class="h-4 w-4" />
                    <span>Nueva Cuenta</span>
                </button>
                <button @click="showLoanModal = true" class="btn-primary rounded-xl shadow-lg shadow-indigo-500/20">
                    <PlusIcon class="h-4 w-4" />
                    <span>Nuevo Préstamo</span>
                </button>
            </div>
        </div>

        <LoadingOverlay :visible="store.loading" />

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            <!-- Investment Card -->
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
                            <span class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">{{ formatCurrency(dashboardStore.summary.invested_assets || 0) }}</span>
                        </div>
                        <p class="text-xs text-zinc-400 mt-2 font-mono">Capital en inventario</p>
                    </div>
                </div>
            </div>

            <div v-for="account in store.accounts" :key="account.id" class="group relative bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-800 hover:shadow-md transition-all duration-300 overflow-hidden">
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

        <!-- Loans Section -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Préstamos Bancarios</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Seguimiento de créditos activos</p>
                </div>
            </div>
            
            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left text-xs lg:text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 dark:text-zinc-400">
                        <tr>
                            <th class="px-3 lg:px-6 py-4 font-medium">Entidad Bancaria</th>
                            <th class="px-3 lg:px-6 py-4 font-medium text-right">Cupo Total</th>
                            <th class="px-3 lg:px-6 py-4 font-medium text-right">Deuda Actual</th>
                            <th class="px-3 lg:px-6 py-4 font-medium text-right">Disponible</th>
                            <th class="px-3 lg:px-6 py-4 font-medium text-center">Estado</th>
                            <th class="px-3 lg:px-6 py-4 font-medium text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        <tr v-if="store.loans.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                No hay préstamos registrados
                            </td>
                        </tr>
                        <tr v-for="loan in store.loans" :key="loan.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-3 lg:px-6 py-4">
                                <div class="flex items-center gap-2 lg:gap-3">
                                    <div class="h-8 w-8 lg:h-10 lg:w-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400 shrink-0">
                                        <BuildingLibraryIcon class="h-4 w-4 lg:h-5 lg:w-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-zinc-900 dark:text-white truncate">{{ loan.bank_name }}</p>
                                        <p class="text-xs text-zinc-500">Tasa: {{ loan.interest_rate }}%</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 lg:px-6 py-4 text-right font-medium text-zinc-900 dark:text-white whitespace-nowrap">
                                {{ formatCurrency(loan.amount) }}
                            </td>
                            <td class="px-3 lg:px-6 py-4 text-right min-w-[120px] lg:min-w-[140px]">
                                <span class="font-bold text-zinc-900 dark:text-white whitespace-nowrap">{{ formatCurrency(loan.current_debt) }}</span>
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 mt-2">
                                    <div class="bg-indigo-600 h-1.5 rounded-full" :style="{ width: `${(loan.current_debt / loan.amount) * 100}%` }"></div>
                                </div>
                            </td>
                            <td class="px-3 lg:px-6 py-4 text-right font-medium text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                {{ formatCurrency(loan.available_credit) }}
                            </td>
                            <td class="px-3 lg:px-6 py-4 text-center">
                                <span :class="getLoanStatusClass(loan.status)" class="inline-flex items-center rounded-full px-2 lg:px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset whitespace-nowrap">
                                    {{ getLoanStatusLabel(loan.status) }}
                                </span>
                            </td>
                            <td class="px-3 lg:px-6 py-4 text-right">
                                <div class="flex flex-col xl:flex-row items-end justify-end gap-2">
                                    <button v-if="loan.available_credit > 0" @click="openDisburseModal(loan)" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-xs bg-indigo-50 dark:bg-indigo-900/20 px-2 lg:px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                                        Desembolsar
                                    </button>
                                    <button v-if="loan.current_debt > 0" @click="openPaymentModal(loan)" class="text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 font-medium text-xs bg-emerald-50 dark:bg-emerald-900/20 px-2 lg:px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
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
                <div v-if="store.loans.length === 0" class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                    No hay préstamos registrados
                </div>
                <div v-for="loan in store.loans" :key="loan.id" class="p-4 space-y-4">
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
                        <button v-if="loan.available_credit > 0" @click="openDisburseModal(loan)" class="w-full text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm bg-indigo-50 dark:bg-indigo-900/20 px-4 py-2 rounded-lg transition-colors">
                            Desembolsar
                        </button>
                        <button v-if="loan.current_debt > 0" @click="openPaymentModal(loan)" class="w-full text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 font-medium text-sm bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 rounded-lg transition-colors">
                            Pagar
                        </button>
                    </div>
                </div>
            </div>
        </div>

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

    </AppContainer>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useFinanceStore } from '../../stores/useFinance';
import { useDashboardsStore } from '../../stores/useDashboards';
import AppContainer from '../../components/ui/AppContainer.vue';
import LoadingOverlay from '../../components/ui/LoadingOverlay.vue';
import { 
    BuildingLibraryIcon, 
    BanknotesIcon, 
    CreditCardIcon, 
    WalletIcon, 
    PlusIcon,
    XMarkIcon,
    TruckIcon
} from '@heroicons/vue/24/outline';

const store = useFinanceStore();
const dashboardStore = useDashboardsStore();

const showAccountModal = ref(false);
const showLoanModal = ref(false);
const showDisburseModal = ref(false);
const showPaymentModal = ref(false);
const selectedLoan = ref(null);

const accountForm = reactive({
    name: '',
    bank_name: '',
    account_number: '',
    type: 'bank',
    initial_balance: 0,
    initial_balance_display: ''
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
