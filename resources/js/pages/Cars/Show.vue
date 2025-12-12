<template>
    <div class="flex flex-col h-screen bg-white dark:bg-zinc-950">
        <!-- Scrollable content area -->
        <div class="flex-1 overflow-y-auto">
            <AppContainer>
                <div v-if="store.loading && !store.currentCar" class="py-8">
                    <LoadingOverlay :visible="true" />
                </div>

                <div v-else-if="store.errors.general" class="py-8">
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center">
                        <ExclamationCircleIcon class="h-12 w-12 mx-auto text-red-600 dark:text-red-400 mb-4" />
                        <h3 class="text-lg font-medium text-red-900 dark:text-red-200 mb-2">Error al cargar vehículo</h3>
                        <p class="text-red-700 dark:text-red-300 mb-4">{{ store.errors.general[0] }}</p>
                        <router-link to="/cars" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <ArrowLeftIcon class="h-4 w-4" />
                            Volver al listado
                        </router-link>
                    </div>
                </div>
                
                <div v-else-if="store.currentCar" class="space-y-6">
                    <!-- Back button -->
                    <router-link to="/cars" class="inline-flex items-center gap-2 text-indigo-600 dark:text-indigo-400 hover:opacity-75 text-sm">
                        <ArrowLeftIcon class="h-4 w-4" />
                        Volver al listado
                    </router-link>

            <!-- Main content: Image + Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Image Gallery (Left) -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-900 rounded-xl overflow-hidden shadow-sm ring-1 ring-zinc-200/70 dark:ring-zinc-800">
                        <!-- Main image -->
                        <div class="relative bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center overflow-hidden">
                            <img v-if="store.currentCar.images && store.currentCar.images[selectedImageIdx]" 
                                :src="`/storage/${store.currentCar.images[selectedImageIdx].path}`" 
                                :alt="`${store.currentCar.brand.name} ${store.currentCar.model}`"
                                class="w-full h-full object-cover" />
                            <div v-else class="text-zinc-400 text-center">
                                <PhotoIcon class="h-12 w-12 mx-auto opacity-50" />
                                <p class="mt-2">Sin imagen</p>
                            </div>

                            <!-- Image counter -->
                            <div v-if="store.currentCar.images?.length" class="absolute top-4 right-4 bg-black/60 text-white px-3 py-1 rounded-full text-xs font-medium">
                                {{ selectedImageIdx + 1 }} / {{ store.currentCar.images.length }}
                            </div>
                        </div>

                        <!-- Thumbnails -->
                        <div v-if="store.currentCar.images?.length" class="flex gap-2 p-4 bg-zinc-50 dark:bg-zinc-800/50 overflow-x-auto">
                            <button v-for="(img, idx) in store.currentCar.images" :key="idx"
                                @click="selectedImageIdx = idx"
                                :class="[selectedImageIdx === idx ? 'ring-2 ring-indigo-500' : 'ring-1 ring-zinc-300 dark:ring-zinc-700 hover:ring-zinc-400', 'h-16 w-20 shrink-0 rounded-lg overflow-hidden']">
                                <img :src="`/storage/${img.path}`" :alt="`Thumbnail ${idx + 1}`" class="w-full h-full object-cover" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Details Card (Right) -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm ring-1 ring-zinc-200/70 dark:ring-zinc-800 p-6 space-y-6">
                        <!-- Title -->
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ store.currentCar.brand.name }} {{ store.currentCar.model }}</h1>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ store.currentCar.year }}</p>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            <span :class="getStatusClass(store.currentCar.status)" class="inline-flex items-center rounded-md px-3 py-1 text-sm font-medium ring-1 ring-inset">
                                {{ estadoLabel(store.currentCar.status) }}
                            </span>
                        </div>

                        <!-- Key Details -->
                        <div class="space-y-3 border-t border-zinc-200 dark:border-zinc-800 pt-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">VIN</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ store.currentCar.vin }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fecha de Compra</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDate(store.currentCar.purchase_date) }}</p>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="space-y-3 border-t border-zinc-200 dark:border-zinc-800 pt-4">
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Precio de Compra</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ formatCurrency(store.currentCar.purchase_price) }}</p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Gastos</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ formatCurrency(totalExpenses) }}</p>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-zinc-200 dark:border-zinc-800">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Costo Total</p>
                                <p class="font-bold text-lg text-gray-900 dark:text-white">{{ formatCurrency(store.currentCar.total_cost) }}</p>
                            </div>
                        </div>

                        <!-- Sale Info if sold -->
                        <div v-if="store.currentCar.status === 'sold'" class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/40 rounded-lg p-4">
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 uppercase tracking-wide font-medium mb-2">Vendido</p>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <p class="text-sm text-emerald-700 dark:text-emerald-300">Precio de Venta</p>
                                    <p class="font-semibold text-emerald-900 dark:text-emerald-100">{{ formatCurrency(store.currentCar.sale_price) }}</p>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-emerald-200 dark:border-emerald-800/40">
                                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Ganancia</p>
                                    <p class="font-bold text-emerald-900 dark:text-emerald-100">{{ formatCurrency(profit) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Expense Section -->
            <div class="relative bg-white dark:bg-zinc-900 rounded-xl shadow-sm ring-1 ring-zinc-200/70 dark:ring-zinc-800 p-6">
                <LoadingOverlay :visible="store.loading" />
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Registrar Gasto</h2>
                <form @submit.prevent="addExpense" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Concepto</label>
                        <input v-model="expenseForm.concept" type="text" required placeholder="ej: Cambio de aceite" class="input" :disabled="store.loading" />
                        <p v-if="store.errors.concept" class="mt-1 text-xs text-red-600">{{ store.errors.concept[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor</label>
                        <input v-model="expenseForm.amount_display" @input="handleExpenseAmountInput" type="text" required placeholder="$ 0" class="input" :disabled="store.loading" />
                        <p v-if="store.errors.amount" class="mt-1 text-xs text-red-600">{{ store.errors.amount[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
                        <input v-model="expenseForm.expense_date" type="date" required class="input" :disabled="store.loading" />
                        <p v-if="store.errors.expense_date" class="mt-1 text-xs text-red-600">{{ store.errors.expense_date[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cuenta</label>
                        <select v-model="expenseForm.account_id" required class="input" :disabled="store.loading">
                            <option value="">Selecciona cuenta</option>
                            <option v-for="acc in financeStore.accounts" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
                        </select>
                        <p v-if="store.errors.account_id" class="mt-1 text-xs text-red-600">{{ store.errors.account_id[0] }}</p>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary flex-1 justify-center" :disabled="store.loading">Agregar Gasto</button>
                    </div>
                </form>
            </div>

            <!-- Sale Price Section -->
            <div class="relative bg-white dark:bg-zinc-900 rounded-xl shadow-sm ring-1 ring-zinc-200/70 dark:ring-zinc-800 p-6">
                <LoadingOverlay :visible="store.loading" />
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Registrar Precio de Venta</h2>
                <form @submit.prevent="updateSalePrice" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Precio Estimado (Opcional)</label>
                        <input v-model="salePriceForm.estimated_price_display" @input="handleEstimatedPriceInput" type="text" placeholder="$ 0" class="input" />
                        <p v-if="store.errors.estimated_price" class="mt-1 text-xs text-red-600">{{ store.errors.estimated_price[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Precio Real de Venta (Opcional)</label>
                        <input v-model="salePriceForm.sale_price_display" @input="handleSalePriceInput" type="text" placeholder="$ 0" class="input" />
                        <p v-if="store.errors.sale_price" class="mt-1 text-xs text-red-600">{{ store.errors.sale_price[0] }}</p>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary flex-1 justify-center">Guardar Precio</button>
                    </div>
                </form>

                <!-- Display current prices if set -->
                <div v-if="store.currentCar.estimated_price || store.currentCar.sale_price" class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-if="store.currentCar.estimated_price" class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <p class="text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wide font-medium mb-1">Precio Estimado</p>
                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ formatCurrency(store.currentCar.estimated_price) }}</p>
                        </div>
                        <div v-if="store.currentCar.sale_price" class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-lg">
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 uppercase tracking-wide font-medium mb-1">Precio Real de Venta</p>
                            <p class="text-2xl font-bold text-emerald-900 dark:text-emerald-100">{{ formatCurrency(store.currentCar.sale_price) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses History -->
            <div v-if="store.currentCar.expenses && store.currentCar.expenses.length > 0" class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm ring-1 ring-zinc-200/70 dark:ring-zinc-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Historial de Gastos</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Concepto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            <tr v-for="expense in store.currentCar.expenses" :key="expense.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">{{ expense.concept }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(expense.expense_date) }}</td>
                                <td class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white font-semibold">{{ formatCurrency(expense.amount) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">Total Gastos</td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-indigo-600 dark:text-indigo-400">{{ formatCurrency(totalExpenses) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Empty expenses state -->
            <div v-else-if="!store.loading" class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-zinc-400 dark:text-zinc-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-gray-600 dark:text-gray-400">No hay gastos registrados para este vehículo</p>
            </div>
        </div>

        <!-- Error fallback -->
        <div v-else class="py-8">
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl p-6 text-center">
                <ExclamationTriangleIcon class="h-12 w-12 mx-auto text-zinc-700 dark:text-zinc-400 mb-4" />
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">Vehículo no encontrado</h3>
                <p class="text-zinc-700 dark:text-zinc-300 mb-4">El vehículo que buscas no existe o fue eliminado.</p>
                <router-link to="/cars" class="inline-flex items-center gap-2 bg-zinc-800 hover:bg-zinc-900 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <ArrowLeftIcon class="h-4 w-4" />
                    Volver al listado
                </router-link>
            </div>
        </div>
            </AppContainer>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useCarsStore } from '../../stores/useCars';
import { useFinanceStore } from '../../stores/useFinance';
import AppContainer from '../../components/ui/AppContainer.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import StatTile from '../../components/ui/StatTile.vue';
import SkeletonRows from '../../components/ui/SkeletonRows.vue';
import LoadingOverlay from '../../components/ui/LoadingOverlay.vue';
import { ArrowLeftIcon, PhotoIcon, ExclamationCircleIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const route = useRoute();
const store = useCarsStore();
const financeStore = useFinanceStore();

const selectedImageIdx = ref(0);

const expenseForm = reactive({
    concept: '',
    amount: null,
    amount_display: '',
    expense_date: new Date().toISOString().split('T')[0],
    account_id: ''
});

const salePriceForm = reactive({
    estimated_price: null,
    estimated_price_display: '',
    sale_price: null,
    sale_price_display: ''
});

onMounted(() => {
    store.fetchCar(route.params.id);
    financeStore.fetchAccounts();
    // Set default account if available
    watch(() => financeStore.accounts, (accounts) => {
        if (accounts && accounts.length > 0 && !expenseForm.account_id) {
            expenseForm.account_id = accounts[0].id;
        }
    }, { immediate: true });
});

const totalExpenses = computed(() => {
    if (!store.currentCar || !store.currentCar.expenses) return 0;
    return store.currentCar.expenses.reduce((sum, exp) => sum + (Number(exp.amount) || 0), 0);
});

const profit = computed(() => {
    if (!store.currentCar) return 0;
    return store.currentCar.sale_price - store.currentCar.total_cost;
});

const addExpense = async () => {
    if (!expenseForm.account_id) {
        alert('Por favor selecciona una cuenta');
        return;
    }
    const payload = {
        concept: expenseForm.concept,
        amount: expenseForm.amount,
        expense_date: expenseForm.expense_date,
        account_id: parseInt(expenseForm.account_id)
    };
    const success = await store.addExpense(store.currentCar.id, payload);
    if (success) {
        // Reset form
        Object.assign(expenseForm, {
            concept: '',
            amount: null,
            amount_display: '',
            expense_date: new Date().toISOString().split('T')[0],
            account_id: financeStore.accounts.length > 0 ? financeStore.accounts[0].id : ''
        });
    }
};

const handleExpenseAmountInput = (event) => {
    let value = event.target.value.replace(/[^\d]/g, '');
    expenseForm.amount = value ? parseInt(value) : null;
    
    if (value) {
        expenseForm.amount_display = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    } else {
        expenseForm.amount_display = '';
    }
};

const updateSalePrice = async () => {
    // Validar que al menos un precio esté presente
    if (!salePriceForm.estimated_price && !salePriceForm.sale_price) {
        alert('Por favor ingresa al menos un precio (estimado o real)');
        return;
    }

    try {
        const payload = {
            estimated_price: salePriceForm.estimated_price,
            sale_price: salePriceForm.sale_price
        };
        
        const response = await store.updateSalePrice(store.currentCar.id, payload);
        if (response) {
            // Reset form after successful update
            Object.assign(salePriceForm, {
                estimated_price: null,
                estimated_price_display: '',
                sale_price: null,
                sale_price_display: ''
            });
        }
    } catch (error) {
        console.error('Error updating sale price:', error);
    }
};

const handleEstimatedPriceInput = (event) => {
    let value = event.target.value.replace(/[^\d]/g, '');
    salePriceForm.estimated_price = value ? parseInt(value) : null;
    
    // Format display value
    if (value) {
        salePriceForm.estimated_price_display = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    } else {
        salePriceForm.estimated_price_display = '';
    }
};

const handleSalePriceInput = (event) => {
    let value = event.target.value.replace(/[^\d]/g, '');
    salePriceForm.sale_price = value ? parseInt(value) : null;
    
    // Format display value
    if (value) {
        salePriceForm.sale_price_display = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    } else {
        salePriceForm.sale_price_display = '';
    }
};

const getStatusClass = (status) => {
    switch (status) {
        case 'available': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 ring-emerald-600/20';
        case 'sold': return 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300 ring-sky-600/20';
        case 'reserved': return 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 ring-amber-600/20';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 ring-gray-600/20';
    }
};

const estadoLabel = (status) => {
    switch (status) {
        case 'available': return 'Disponible';
        case 'sold': return 'Vendido';
        case 'reserved': return 'Apartado';
        default: return 'Desconocido';
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('es-CO', options);
};
</script>
