<template>
    <div class="flex flex-col h-screen bg-white dark:bg-zinc-950">
        <!-- Scrollable content area (banner + content) -->
        <div class="flex-1 overflow-y-auto">
            <!-- Banner hero - Scrolls with content -->
            <div class="relative overflow-hidden w-full">
                <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1600&auto=format&fit=crop" alt="Banner vehículos" class="w-full h-55 md:h-80 object-cover" />
                <div class="absolute inset-0 bg-linear-to-t from-black/40 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-4 md:bottom-6 flex justify-center">
                </div>
            </div>

            <AppContainer>
            <PageHeader title="Vehículos" />
        <LoadingOverlay :visible="store.loading && store.cars.length === 0" />
        <!-- Toast notifications -->
        <transition name="fade">
            <div v-if="toast.message" :class="toastClass" class="fixed right-6 top-6 z-50 rounded-lg px-4 py-2 shadow">
                {{ toast.message }}
            </div>
        </transition>
        
        <!-- Barra de búsqueda y filtros -->
        <div class="mt-4 space-y-3 lg:space-y-0">
            <!-- Búsqueda -->
            <div>
                <label class="sr-only">Buscar</label>
                <div class="relative">
                    <input v-model="search" @input="debouncedFetch()" type="text" placeholder="Buscar por modelo, VIN, placa..." class="input input-center w-full pr-10 py-5" />
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400">
                        <MagnifyingGlassIcon class="h-5 w-5" />
                    </div>
                    <button v-if="search" @click="clearSearch" class="absolute right-9 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-700">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <!-- Filtros y botones -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-stretch sm:items-center sm:justify-end mt-5">
                <select v-model="filters.status" @change="fetchData()" class="input text-sm w-full sm:w-auto">
                    <option value="">Todos los estados</option>
                    <option value="available">Disponible</option>
                    <option value="sold">Vendido</option>
                    <option value="reserved">Apartado</option>
                </select>
                <select v-model="filters.brand_id" @change="fetchData()" class="input text-sm w-full sm:w-auto">
                    <option value="">Todas las marcas</option>
                    <option v-for="brand in store.brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                </select>
                <button @click="resetFilters" class="inline-flex items-center justify-center rounded-md border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 w-full sm:w-auto whitespace-nowrap">Restablecer</button>
                <button class="btn-primary inline-flex items-center justify-center cursor-pointer w-full sm:w-auto whitespace-nowrap" @click="showCreateModal = true">Nuevo vehículo</button>
            </div>
        </div>

        <div v-if="store.loading" class="mt-6">
            <SkeletonRows :count="5" />
        </div>

        <div v-else class="mt-6">
            <div v-if="store.errors.general" class="mb-4 text-sm text-red-600">
                {{ store.errors.general[0] }}
            </div>
            <!-- Tarjetas estilo catálogo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <div v-for="car in store.cars" :key="car.id" class="rounded-2xl ring-1 ring-zinc-200/70 dark:ring-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                    <div class="h-40 bg-zinc-100 dark:bg-zinc-800">
                        <img v-if="car.image_url" :src="car.image_url" :alt="`${car.brand.name} ${car.model}`" class="h-full w-full object-cover" />
                        <div v-else class="h-full w-full flex items-center justify-center text-zinc-400">Sin imagen</div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ car.brand.name }} {{ car.model }}</h3>
                                <p class="text-xs text-zinc-500">{{ car.year }}</p>
                            </div>
                            <span :class="getStatusClass(car.status)" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset">
                                {{ estadoLabel(car.status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-zinc-500">Precio de compra</p>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ formatCurrency(car.purchase_price) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-zinc-500">Precio de venta</p>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ car.sale_price ? formatCurrency(car.sale_price) : '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">VIN: {{ car.vin }}</p>
                            <router-link :to="{ name: 'cars.show', params: { id: car.id }}" class="inline-flex items-center rounded-md bg-indigo-600 text-white px-3 py-1.5 text-xs font-semibold hover:bg-indigo-500">Ver detalles</router-link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-6 flex items-center justify-between">
                <p class="text-sm text-zinc-600">Mostrando página {{ store.pagination.current_page }} de {{ store.pagination.last_page }}</p>
                <div class="flex items-center gap-2">
                    <button class="inline-flex items-center rounded-md border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800" :disabled="store.pagination.current_page <= 1" @click="goToPage(store.pagination.current_page - 1)">Anterior</button>
                    <button class="inline-flex items-center rounded-md border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800" :disabled="store.pagination.current_page >= store.pagination.last_page" @click="goToPage(store.pagination.current_page + 1)">Siguiente</button>
                </div>
            </div>
            
            <div v-if="store.cars.length === 0" class="mt-6">
                 <EmptyState message="No se encontraron resultados. Intenta ajustar la búsqueda o filtros." />
            </div>
        </div>

        <!-- Create Modal (Simplified) -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Agregar vehículo</h3>
                <form @submit.prevent="createCar" class="space-y-4">
                    <div v-if="successMessage" class="rounded-lg bg-emerald-50 text-emerald-700 px-3 py-2 text-sm">
                        {{ successMessage }}
                    </div>
                    <!-- Imágenes primero -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Imágenes</label>
                        <input ref="imagesInput" type="file" multiple accept="image/*" class="hidden" @change="onImagesSelected" />
                        
                        <!-- Drag and drop area -->
                        <div 
                            @click="() => imagesInput?.click()"
                            @dragover.prevent
                            @drop.prevent="onImagesSelected"
                            class="relative border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg p-8 text-center cursor-pointer hover:border-indigo-500 dark:hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/20 transition"
                        >
                            <div class="flex flex-col items-center gap-3">
                                <!-- Upload icon -->
                                <div class="w-12 h-12 mx-auto bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center">
                                    <ArrowUpTrayIcon class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Arrastra imágenes aquí</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">o haz clic para seleccionar</p>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500">PNG, JPG, GIF hasta 10MB</p>
                            </div>
                        </div>
                        
                        <!-- Selected images count -->
                        <p v-if="newCar.images?.length" class="mt-3 text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                            ✓ {{ newCar.images.length }} imagen(es) seleccionada(s)
                        </p>
                        
                        <!-- Image previews -->
                        <div v-if="imagePreviews.length" class="mt-4 grid grid-cols-3 gap-3">
                            <div v-for="(src, idx) in imagePreviews" :key="idx" class="relative group">
                                <img :src="src" class="h-24 w-full object-cover rounded-lg" />
                                <button
                                    type="button"
                                    @click.stop="removeImage(idx)"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition"
                                >
                                    <XMarkIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                        
                        <p v-if="store.errors['images.0']" class="mt-2 text-xs text-red-600">{{ store.errors['images.0'][0] }}</p>
                    </div>

                    <!-- Marca con buscador y texto libre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Marca</label>
                        <div class="relative mt-1">
                            <input v-model="brandInput" type="text" class="input" placeholder="Escribe o selecciona la marca" @input="onBrandInput" />
                            <div v-if="brandSuggestions.length" class="absolute z-10 mt-1 w-full rounded-lg bg-white dark:bg-zinc-900 shadow ring-1 ring-zinc-200/70 dark:ring-zinc-800">
                                <ul>
                                    <li v-for="b in brandSuggestions" :key="b.id" @click="selectBrand(b)" class="px-3 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer">{{ b.name }}</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-zinc-500">Si no aparece la marca, puedes escribirla y la crearemos.</p>
                        <p v-if="store.errors.brand_id" class="mt-1 text-xs text-red-600">{{ store.errors.brand_id[0] }}</p>
                        <p v-if="store.errors.brand_name" class="mt-1 text-xs text-red-600">{{ store.errors.brand_name[0] }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modelo</label>
                            <input v-model="newCar.model" type="text" required class="input mt-1" />
                            <p v-if="store.errors.model" class="mt-1 text-xs text-red-600">{{ store.errors.model[0] }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Año</label>
                            <input v-model="newCar.year" type="number" required class="input mt-1" />
                            <p v-if="store.errors.year" class="mt-1 text-xs text-red-600">{{ store.errors.year[0] }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">VIN</label>
                        <input v-model="newCar.vin" type="text" required class="input mt-1" />
                        <p v-if="store.errors.vin" class="mt-1 text-xs text-red-600">{{ store.errors.vin[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio de compra</label>
                        <input :value="purchasePriceDisplay" @input="onPriceInput" inputmode="numeric" required class="input mt-1" />
                        <p v-if="store.errors.purchase_price" class="mt-1 text-xs text-red-600">{{ store.errors.purchase_price[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de compra</label>
                        <input v-model="newCar.purchase_date" type="date" required class="input mt-1" />
                        <p v-if="store.errors.purchase_date" class="mt-1 text-xs text-red-600">{{ store.errors.purchase_date[0] }}</p>
                    </div>
                    <!-- Removido campo de cuenta de pago; se usará cuenta por defecto en backend/tienda -->
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-zinc-800 dark:text-gray-300 dark:border-zinc-700">Cancelar</button>
                        <button type="submit" class="btn-primary" :disabled="store.loading">{{ store.loading ? 'Guardando…' : 'Guardar' }}</button>
                    </div>
                </form>
            </div>
        </div>
        </AppContainer>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { watch } from 'vue';
import { useCarsStore } from '../../stores/useCars';
import { useFinanceStore } from '../../stores/useFinance';
import AppContainer from '../../components/ui/AppContainer.vue';
import PageHeader from '../../components/ui/PageHeader.vue';
import DataToolbar from '../../components/ui/DataToolbar.vue';
import SkeletonRows from '../../components/ui/SkeletonRows.vue';
import EmptyState from '../../components/ui/EmptyState.vue';
import LoadingOverlay from '../../components/ui/LoadingOverlay.vue';
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { ArrowUpTrayIcon } from '@heroicons/vue/24/solid';

const store = useCarsStore();
const financeStore = useFinanceStore();

const search = ref('');
let searchTimer = null;
const filters = reactive({
    status: 'available',
    brand_id: '',
    year_from: '',
    year_to: ''
});
const brandQuery = ref('');
const filteredBrands = computed(() => {
    const q = brandQuery.value.toLowerCase();
    if (!q) return store.brands;
    return store.brands.filter(b => b.name.toLowerCase().includes(q));
});
const showCreateModal = ref(false);
const successMessage = ref('');
const toast = reactive({ message: '', type: 'success' });
const toastClass = computed(() => {
    return toast.type === 'error'
        ? 'bg-red-600 text-white'
        : 'bg-emerald-600 text-white';
});
const showToast = (msg, type = 'success') => {
    toast.message = msg;
    toast.type = type;
    setTimeout(() => { toast.message = ''; }, 2500);
};
const imagePreviews = ref([]);
const imagesInput = ref(null);
const brandInput = ref('');
const brandSuggestions = computed(() => {
    const q = brandInput.value.trim().toLowerCase();
    if (!q) return [];
    const brands = Array.isArray(store.brands) ? store.brands : [];
    return brands.filter(b => b.name.toLowerCase().includes(q)).slice(0, 8);
});

const newCar = reactive({
    brand_id: '',
    brand_name: '',
    model: '',
    year: new Date().getFullYear(),
    vin: '',
    purchase_price: '',
    purchase_date: new Date().toISOString().split('T')[0],
    account_id: ''
});

onMounted(() => {
    fetchData();
    store.fetchBrands();
    financeStore.fetchAccounts();
});

// Los cambios en filtros disparan la búsqueda
const onFiltersChange = () => fetchData();

// unified fetch handler handled by store; do not redeclare elsewhere
const page = ref(1);

const fetchData = async () => {
    const brandParam = (filters.brand_id === 'all' || filters.brand_id === '') ? undefined : filters.brand_id;
    await store.fetchCars({
        page: page.value,
        status: filters.status,
        search: search.value,
        brand_id: brandParam,
        year_from: filters.year_from,
        year_to: filters.year_to,
        purchase_date_from: filters.purchase_date_from,
        purchase_date_to: filters.purchase_date_to,
    });
};

// initial load handled above in onMounted

// react to filter changes
watch(() => filters.status, () => fetchData());
watch(() => filters.brand_id, () => fetchData());
watch(() => filters.year_from, () => fetchData());
watch(() => filters.year_to, () => fetchData());
watch(() => filters.purchase_date_from, () => fetchData());
watch(() => filters.purchase_date_to, () => fetchData());

// debounce search input
let searchDebounce;
watch(search, () => {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => fetchData(), 300);
});
const debouncedFetch = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => fetchData(), 300);
};

const goToPage = (p) => {
    page.value = p;
    fetchData();
};

const clearSearch = () => {
    search.value = '';
    fetchData();
};

const resetFilters = () => {
    filters.status = '';
    filters.brand_id = '';
    fetchData();
};

const createCar = async () => {
    const success = await store.createCar(newCar);
    if (success) {
        successMessage.value = 'Vehículo creado correctamente.';
        setTimeout(() => { successMessage.value = ''; }, 2500);
        showToast('Vehículo creado correctamente.', 'success');
        showCreateModal.value = false;
        fetchData();
        // Reset form
        Object.assign(newCar, {
            brand_id: '',
            model: '',
            year: new Date().getFullYear(),
            vin: '',
            purchase_price: '',
            purchase_date: new Date().toISOString().split('T')[0],
            account_id: '',
            images: []
        });
        imagePreviews.value = [];
        brandInput.value = '';
    } else {
        showToast('No se pudo crear el vehículo. Revisa los campos.', 'error');
    }
};

const onImagesSelected = (e) => {
    const files = Array.from(e.target.files || []);
    newCar.images = files;
    imagePreviews.value = files.map(f => URL.createObjectURL(f));
};

const removeImage = (idx) => {
    newCar.images = newCar.images.filter((_, i) => i !== idx);
    imagePreviews.value = imagePreviews.value.filter((_, i) => i !== idx);
};

const onBrandInput = () => {
    const brands = Array.isArray(store.brands) ? store.brands : [];
    const match = brands.find(b => b.name.toLowerCase() === brandInput.value.trim().toLowerCase());
    if (match) {
        newCar.brand_id = match.id;
        newCar.brand_name = '';
    } else {
        newCar.brand_id = '';
        newCar.brand_name = brandInput.value.trim();
    }
};

const selectBrand = (b) => {
    brandInput.value = b.name;
    newCar.brand_id = b.id;
    newCar.brand_name = '';
};

const purchasePriceDisplay = computed(() => {
    const n = Number(newCar.purchase_price || 0);
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(n);
});

const onPriceInput = (e) => {
    const raw = e.target.value.replace(/[^\d]/g, '');
    newCar.purchase_price = raw ? Number(raw) : '';
    e.target.value = purchasePriceDisplay.value;
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
</script>
