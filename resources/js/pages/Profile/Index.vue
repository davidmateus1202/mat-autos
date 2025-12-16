<template>
    <div class="relative min-h-screen bg-slate-50 dark:bg-zinc-950 overflow-x-hidden">
        <LoadingOverlay :visible="authStore.loading" />

        <div class="w-full space-y-8">
            <header
                class="relative overflow-hidden dark:bg-zinc-900 shadow-2xl ring-1 ring-zinc-200/70 dark:ring-zinc-800 flex py-8 sm:py-10 w-full"
                style="max-height: 700px;">
                <!-- Imagen del banner: ocupa todo el header por ser inset-0 -->
                <div class="absolute inset-0" :style="bannerStyle"></div>

                <!-- Degradado oscuro mejorado (inline para evitar clase Tailwind inválida) -->
                <div class="absolute inset-0 z-20"
                    style="background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.3), rgba(0,0,0,0.7));">
                </div>

                <!-- Contenedor con altura relativa -->
                <div class="relative h-full flex w-full items-end">
                    <div class="w-full px-4 sm:px-6 lg:px-8">
                        <!-- Contenido anclado al fondo -->
                        <div
                            class="flex flex-col sm:flex-row gap-4 sm:gap-6 pb-6 sm:pb-8 md:pb-12 text-white">
                            <div class="flex flex-col items-center sm:items-start sm:flex-row gap-4 sm:gap-6 w-full">
                                <!-- Avatar grande y prominente -->
                                <div
                                    class="h-24 w-24 sm:h-28 md:h-32 sm:w-28 md:w-32 shrink-0 rounded-full bg-black text-white flex items-center justify-center text-3xl sm:text-4xl md:text-5xl font-bold ring-4 ring-white shadow-2xl">
                                    {{ initials }}
                                </div>

                                <div class="space-y-2 sm:space-y-3 drop-shadow z-50 text-center sm:text-left flex-1">
                                    <div>
                                        <h1 class="text-3xl sm:text-4xl md:text-6xl font-bold leading-tight text-white mb-0 sm:mb-1">{{
                                            userName }}</h1>
                                        <p class="text-xs sm:text-sm md:text-lg text-white/80">{{ userRole }}</p>
                                    </div>

                                    <div class="flex flex-col gap-1 sm:gap-2 text-xs sm:text-sm">
                                        <p class="text-white/90 flex items-center gap-2 justify-center sm:justify-start">
                                            <EnvelopeIcon class="h-4 w-4 sm:h-5 sm:w-5 shrink-0" />
                                            <span class="truncate">{{ userEmail }}</span>
                                        </p>

                                        <p class="text-white/90 flex items-center gap-2 justify-center sm:justify-start">
                                            <CalendarDaysIcon class="h-4 w-4 sm:h-5 sm:w-5 shrink-0" />
                                            <span>Miembro desde {{ joinedAt }}</span>
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2 pt-1 sm:pt-2 justify-center sm:justify-start">
                                        <span v-if="isVerified"
                                            class="inline-flex items-center gap-2 rounded-full bg-emerald-100 text-emerald-800 px-3 sm:px-4 py-1 sm:py-1.5 text-xs font-semibold shadow-md">
                                            <ShieldCheckIcon class="h-3 w-3 sm:h-4 sm:w-4" />
                                            <span>Correo verificado</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Cierre del header -->
                        </div>
                    </div>
                </div>
            </header>



            <section class="flex items-center justify-center w-full px-4 sm:px-6 lg:px-8">
                <div class="card w-full max-w-2xl">
                    <div class="mb-6">
                        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Perfil</p>
                        <h2 class="text-lg sm:text-xl font-semibold text-zinc-900 dark:text-white">Actualizar información</h2>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">Sincronizado con tu cuenta actual. Edita tu
                            nombre o correo y guarda los cambios.</p>
                    </div>

                    <form class="space-y-5" @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="field">
                                <span class="label text-xs sm:text-sm">Nombre completo</span>
                                <input v-model="form.name" type="text" class="input text-sm" />
                            </label>
                            <label class="field">
                                <span class="label text-xs sm:text-sm">Correo</span>
                                <input v-model="form.email" type="email" class="input text-sm" />
                            </label>
                            <label class="field">
                                <span class="label text-xs sm:text-sm">Rol</span>
                                <input v-model="form.role" type="text" class="input text-sm" disabled />
                            </label>
                            <label class="field">
                                <span class="label text-xs sm:text-sm">Miembro desde</span>
                                <input :value="joinedAt" type="text" class="input text-sm" disabled />
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-black text-white px-4 py-2 text-xs sm:text-sm font-semibold hover:bg-black/85 cursor-pointer focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-teal-600 transition w-full sm:w-auto">
                                Guardar cambios
                            </button>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">Los datos se cargan desde tu sesión
                                actual.</span>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, watch } from 'vue'
import { useAuthStore } from '../../stores/useAuth'
import { EnvelopeIcon, CalendarDaysIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline'
import LoadingOverlay from '../../components/ui/LoadingOverlay.vue'

const authStore = useAuthStore()
const form = reactive({ name: '', email: '', role: '' })

onMounted(async () => {
    if (!authStore.user) {
        await authStore.getUser()
    }
})

watch(
    () => authStore.user,
    (user) => {
        form.name = user?.name || 'Usuario'
        form.email = user?.email || 'correo@demo.com'
        form.role = user?.role || 'Usuario'
    },
    { immediate: true }
)

const userName = computed(() => authStore.user?.name || 'Usuario')
const userEmail = computed(() => authStore.user?.email || 'correo@demo.com')
const userRole = computed(() => authStore.user?.role || 'Usuario')
const isVerified = computed(() => !!authStore.user?.email_verified_at)

const initials = computed(() => {
    const name = userName.value.trim()
    if (!name) return 'U'
    const parts = name.split(' ')
    const first = parts[0]?.[0] || ''
    const second = parts[1]?.[0] || ''
    return `${first}${second}`.toUpperCase() || 'U'
})

const joinedAt = computed(() => {
    const raw = authStore.user?.created_at
    if (!raw) return 'Fecha no disponible'
    const date = new Date(raw)
    if (Number.isNaN(date.getTime())) return 'Fecha no disponible'
    return new Intl.DateTimeFormat('es-ES', { day: '2-digit', month: 'short', year: 'numeric' }).format(date)
})

const bannerUrl = computed(() => {
    const envUrl = import.meta.env.VITE_PROFILE_BANNER_URL
    return envUrl || 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?q=80&w=1600&auto=format&fit=crop'
})

const bannerStyle = computed(() => ({
    backgroundImage: `url('${bannerUrl.value}')`,
    backgroundSize: 'cover',
    backgroundPosition: 'center'
}))

const handleSubmit = () => {
    authStore.user = {
        ...authStore.user,
        name: form.name,
        email: form.email,
        role: form.role
    }
}
</script>

<style scoped>
.card {
    border-radius: 1rem;
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    width: 100%;
}

.dark .card {
    background-color: #111827;
    border-color: #1f2937;
}

@media (min-width: 768px) {
    .card {
        padding: 2rem;
    }
}

.field {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6b7280;
}

.dark .label {
    color: #9ca3af;
}

.value {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

.dark .value {
    color: #f3f4f6;
}

.input {
    width: 100%;
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    background-color: #ffffff;
    color: #111827;
    padding: 0.75rem;
    font-size: 0.875rem;
    outline: none;
    transition: all 0.2s;
}

.dark .input {
    border-color: #4b5563;
    background-color: #111827;
    color: #f3f4f6;
}

.input:focus {
    border-color: #0d9488;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
}

.input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
