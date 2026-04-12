<template>
    <div class="bg-white text-zinc-900">
        <AppContainer>
            <header class="flex flex-wrap items-center justify-between gap-4 py-6">
                <div class="flex items-center gap-2">
                    <span class="text-lg font-bold text-zinc-900">Zoom</span>
                    <span class="text-lg font-bold text-zinc-900">Car Rental</span>
                </div>

                <nav class="hidden items-center gap-6 text-xs font-semibold uppercase tracking-wide text-zinc-500 md:flex">
                    <a href="#inicio" class="transition hover:text-zinc-900">Home</a>
                    <a href="#funciones" class="transition hover:text-zinc-900">Features</a>
                    <a href="#gestion" class="transition hover:text-zinc-900">Gestion</a>
                    <a href="#vehiculos" class="transition hover:text-zinc-900">Vehiculos</a>
                </nav>

                <RouterLink
                    :to="authStore.isAuthenticated ? '/dashboard' : '/login'"
                    class="rounded-md bg-zinc-900 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-zinc-700"
                >
                    {{ authStore.isAuthenticated ? 'Dashboard' : 'Registrate' }}
                </RouterLink>
            </header>

            <section id="inicio" class="grid items-center gap-8 pb-12 pt-4 lg:grid-cols-[1fr_1.1fr]">
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-zinc-700">Sistema financiero automotriz</p>
                    <h1 class="text-4xl font-bold leading-tight sm:text-5xl">Find Your Best Car Business Flow</h1>
                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-zinc-600 sm:text-base">
                        Controla inventario, gastos por vehiculo, cuentas financieras y rentabilidad en una sola plataforma con datos reales.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <RouterLink
                            :to="authStore.isAuthenticated ? '/dashboard' : '/login'"
                            class="rounded-md bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-zinc-700"
                        >
                            {{ authStore.isAuthenticated ? 'Ir al panel' : 'Probar ahora' }}
                        </RouterLink>
                        <RouterLink to="/login" class="rounded-md border border-zinc-300 px-5 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                            Iniciar sesion
                        </RouterLink>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <article v-for="metric in heroMetrics" :key="metric.label" class="rounded-xl border border-zinc-200 bg-white p-3 shadow-sm">
                            <p class="text-lg font-bold text-zinc-900">{{ metric.value }}</p>
                            <p class="text-xs text-zinc-500">{{ metric.label }}</p>
                        </article>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-y-4 right-0 hidden w-[85%] rounded-l-[120px] bg-zinc-100 lg:block"></div>
                    <div class="relative z-10 rounded-2xl bg-white p-2">
                        <img
                            :src="heroImage"
                            alt="Carro deportivo amarillo"
                            class="h-72 w-full rounded-2xl object-cover sm:h-96"
                            @error="handleImageError"
                        />
                    </div>
                    <div class="absolute -left-2 top-4 hidden rounded-lg border border-zinc-200 bg-white px-3 py-2 shadow-md sm:block">
                        <p class="text-[11px] font-semibold text-zinc-500">Balance disponible</p>
                        <p class="text-sm font-bold text-emerald-600">{{ formatMoney(summary.available_cash) }}</p>
                    </div>
                </div>
            </section>
        </AppContainer>

        <section id="funciones" class="border-y border-zinc-100 bg-white py-12">
            <AppContainer>
                <h2 class="text-center text-3xl font-bold">Awesome Features</h2>
                <div class="mx-auto mt-8 grid max-w-5xl gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <article v-for="feature in features" :key="feature.title" class="rounded-xl border border-zinc-200 bg-white p-5 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-zinc-100 text-zinc-900">
                            <component :is="feature.icon" class="h-6 w-6" />
                        </div>
                        <h3 class="text-sm font-bold text-zinc-900">{{ feature.title }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-zinc-500">{{ feature.description }}</p>
                    </article>
                </div>
            </AppContainer>
        </section>

        <section id="gestion" class="bg-zinc-50/60 py-12">
            <AppContainer>
                <div class="grid items-center gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="relative rounded-2xl bg-white p-2 shadow-sm">
                        <div class="absolute -left-4 top-8 h-44 w-44 rounded-full bg-zinc-100"></div>
                        <img
                            :src="secondaryImage"
                            alt="Sedan blanco premium"
                            class="relative z-10 h-72 w-full rounded-2xl object-cover"
                            @error="handleImageError"
                        />
                    </div>

                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-700">Book your car flow</p>
                        <h3 class="mt-2 text-2xl font-bold text-zinc-900">Panel rapido de gestion</h3>

                        <div class="mt-5 space-y-3">
                            <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm text-zinc-700">Vehiculos disponibles: {{ summary.available_cars }}</div>
                            <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm text-zinc-700">Vendidos este mes: {{ summary.cars_sold_month }}</div>
                            <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm text-zinc-700">Utilidad neta: {{ formatMoney(summary.net_profit_month) }}</div>
                            <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm text-zinc-700">Gastos del mes: {{ formatMoney(summary.expenses_month) }}</div>
                        </div>

                        <RouterLink
                            :to="authStore.isAuthenticated ? '/dashboard' : '/login'"
                            class="mt-5 inline-flex rounded-md bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-700"
                        >
                            Continuar
                        </RouterLink>
                    </div>
                </div>
            </AppContainer>
        </section>

        <section id="vehiculos" class="bg-white py-14">
            <AppContainer>
                <h2 class="text-center text-3xl font-bold">Choose Awesome Car</h2>
                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <article v-for="car in showcaseCars" :key="car.name" class="rounded-xl border border-zinc-200 bg-white p-3 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <img :src="car.image" :alt="car.name" class="h-32 w-full rounded-lg object-cover" @error="handleImageError" />
                        <h3 class="mt-3 text-sm font-bold text-zinc-900">{{ car.name }}</h3>
                        <p class="mt-1 text-xs font-medium text-zinc-500">{{ car.type }}</p>
                        <div class="mt-2 text-xs text-zinc-600">
                            <p>Compra: {{ car.purchase }}</p>
                            <p>Venta: {{ car.sale }}</p>
                        </div>
                    </article>
                </div>
            </AppContainer>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import {
    BanknotesIcon,
    ChartBarSquareIcon,
    LifebuoyIcon,
    ShieldCheckIcon,
} from '@heroicons/vue/24/outline'
import AppContainer from '../components/ui/AppContainer.vue'
import { useAuthStore } from '../stores/useAuth'
import { useCarsStore } from '../stores/useCars'
import { useDashboardsStore } from '../stores/useDashboards'

const authStore = useAuthStore()
const carsStore = useCarsStore()
const dashboardsStore = useDashboardsStore()

const landingLoading = ref(true)

const emergencyImage = 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1600&auto=format&fit=crop'
const secondaryEmergencyImage = 'https://images.unsplash.com/photo-1493238792000-8113da705763?q=80&w=1600&auto=format&fit=crop'
const inlinePlaceholder = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(`
<svg xmlns='http://www.w3.org/2000/svg' width='1200' height='700' viewBox='0 0 1200 700'>
  <rect width='1200' height='700' fill='#f4f4f5'/>
  <rect x='100' y='120' width='1000' height='460' rx='30' fill='#e4e4e7'/>
  <text x='600' y='360' text-anchor='middle' fill='#52525b' font-family='Arial, sans-serif' font-size='44'>Car Image</text>
</svg>
`)}`

const fallbackImages = [
    emergencyImage,
    'https://images.unsplash.com/photo-1617531653332-bd46c24f2068?q=80&w=1600&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?q=80&w=1600&auto=format&fit=crop',
    secondaryEmergencyImage,
]

const features = [
    {
        icon: ShieldCheckIcon,
        title: 'Simple Booking',
        description: 'Registro rapido de compra, venta y estado por vehiculo.'
    },
    {
        icon: BanknotesIcon,
        title: 'Expense Tracking',
        description: 'Control de gastos con impacto directo en utilidad neta.'
    },
    {
        icon: ChartBarSquareIcon,
        title: 'Secure Payment Flow',
        description: 'Movimientos financieros auditables por cuenta.'
    },
    {
        icon: LifebuoyIcon,
        title: 'Customer Support',
        description: 'Gestion operativa clara para decisiones comerciales.'
    }
]

const summary = computed(() => dashboardsStore.summary)

const heroImage = computed(() => {
    const firstCarWithImage = carsStore.cars.find((car) => car.image_url)
    return firstCarWithImage?.image_url || fallbackImages[0]
})

const secondaryImage = computed(() => {
    return carsStore.cars[1]?.image_url || fallbackImages[1]
})

const heroMetrics = computed(() => {
    if (landingLoading.value) {
        return [
            { value: '...', label: 'Cargando datos' },
            { value: '...', label: 'Cargando datos' },
            { value: '...', label: 'Cargando datos' }
        ]
    }

    if (!authStore.isAuthenticated) {
        return [
            { value: 'Inventario + Finanzas', label: 'Operacion unificada' },
            { value: 'Control por vehiculo', label: 'Costos y utilidad real' },
            { value: 'Dashboard centralizado', label: 'Decision basada en datos' }
        ]
    }

    return [
        { value: String(summary.value.available_cars || 0), label: 'Vehiculos disponibles' },
        { value: formatMoney(summary.value.sales_revenue_month), label: 'Ventas del mes' },
        { value: formatMoney(summary.value.potential_inventory_profit), label: 'Utilidad potencial' }
    ]
})

const showcaseCars = computed(() => {
    if (!authStore.isAuthenticated || carsStore.cars.length === 0) {
        return [
            {
                name: 'Porsche 911 Turbo',
                type: 'Deportivo',
                purchase: '$138.000.000',
                sale: '$159.000.000',
                image: fallbackImages[0],
            },
            {
                name: 'Nissan GT-R',
                type: 'Performance',
                purchase: '$112.000.000',
                sale: '$128.500.000',
                image: fallbackImages[1],
            },
            {
                name: 'Ferrari Roma',
                type: 'Supercar',
                purchase: '$226.000.000',
                sale: '$259.900.000',
                image: fallbackImages[2],
            },
            {
                name: 'Lamborghini Huracan',
                type: 'Sport',
                purchase: '$320.000.000',
                sale: '$355.000.000',
                image: fallbackImages[3],
            }
        ]
    }

    return carsStore.cars.slice(0, 4).map((car, index) => ({
        name: `${car.brand?.name ? `${car.brand.name} ` : ''}${car.model || 'Vehiculo'} ${car.year || ''}`.trim(),
        type: car.status === 'sold' ? 'Vendido' : car.status === 'reserved' ? 'Reservado' : 'Disponible',
        purchase: formatMoney(car.purchase_price),
        sale: car.sale_price ? formatMoney(car.sale_price) : 'Sin venta',
        image: car.image_url || fallbackImages[index % fallbackImages.length],
    }))
})

onMounted(async () => {
    try {
        await authStore.getUser()

        if (authStore.isAuthenticated) {
            await Promise.all([
                dashboardsStore.fetchAll(),
                carsStore.fetchCars({ page: 1 })
            ])
        }
    } finally {
        landingLoading.value = false
    }
})

function handleImageError(event) {
    const image = event?.target
    if (!image) return

    const triedPrimaryFallback = image.dataset.fallbackStage === '1'
    const triedSecondaryFallback = image.dataset.fallbackStage === '2'

    if (!triedPrimaryFallback) {
        image.dataset.fallbackStage = '1'
        image.src = emergencyImage
        return
    }

    if (!triedSecondaryFallback) {
        image.dataset.fallbackStage = '2'
        image.src = secondaryEmergencyImage
        return
    }

    image.src = inlinePlaceholder
}

function formatMoney(value) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(Number(value || 0))
}
</script>
