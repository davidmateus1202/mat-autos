<template>
    <div class="relative min-h-screen bg-slate-50 dark:bg-zinc-950">
        <LoadingOverlay :visible="authStore.loading" />

        <div class="mx-auto space-y-8">
            <header
                class="relative overflow-hidden dark:bg-zinc-900 shadow-2xl ring-1 ring-zinc-200/70 dark:ring-zinc-800 flex py-10"
                style="max-height: 700px;">
                <!-- Imagen del banner: ocupa todo el header por ser inset-0 -->
                <div class="absolute inset-0" :style="bannerStyle"></div>

                <!-- Degradado oscuro mejorado (inline para evitar clase Tailwind inválida) -->
                <div class="absolute inset-0 z-20"
                    style="background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.3), rgba(0,0,0,0.7));">
                </div>

                <!-- Contenedor con altura relativa -->
                <div class="relative h-full flex w-full md:w-auto items-end">
                    <div class="w-full px-4 sm:px-6 lg:px-8">
                        <!-- Contenido anclado al fondo -->
                        <div
                            class="flex flex-col sm:flex-row gap-6 pb-8 sm:pb-12 text-white">
                            <div class="flex flex-col items-center justify-center md:flex-row md:items-end gap-4 sm:gap-6">
                                <!-- Avatar grande y prominente -->
                                <div
                                    class="h-32 w-32 sm:h-32 sm:w-32 rounded-full bg-black text-white flex items-center justify-center text-5xl font-bold ring-4 ring-white shadow-2xl">
                                    {{ initials }}
                                </div>

                                <div class="space-y-3 drop-shadow z-50">
                                    <div>
                                        <h1 class="text-5xl sm:text-6xl font-bold leading-tight text-white mb-1">{{
                                            userName }}</h1>
                                        <p class="text-base sm:text-lg text-white/80">{{ userRole }}</p>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <p class="text-sm text-white/90 flex items-center gap-2">
                                            <EnvelopeIcon class="h-5 w-5 flex-shrink-0" />
                                            <span>{{ userEmail }}</span>
                                        </p>

                                        <p class="text-sm text-white/90 flex items-center gap-2">
                                            <CalendarDaysIcon class="h-5 w-5 flex-shrink-0" />
                                            <span>Miembro desde {{ joinedAt }}</span>
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2 pt-2">
                                        <span v-if="isVerified"
                                            class="inline-flex items-center gap-2 rounded-full bg-emerald-100 text-emerald-800 px-4 py-1.5 text-xs font-semibold shadow-md">
                                            <ShieldCheckIcon class="h-4 w-4" />
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



            <section class="flex items-center justify-center w-full px-5 md:px-36">
                <div class="card">
                    <div class="mb-6">
                        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Perfil</p>
                        <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Actualizar información</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Sincronizado con tu cuenta actual. Edita tu
                            nombre o correo y guarda los cambios.</p>
                    </div>

                    <form class="space-y-5" @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="field">
                                <span class="label">Nombre completo</span>
                                <input v-model="form.name" type="text" class="input" />
                            </label>
                            <label class="field">
                                <span class="label">Correo</span>
                                <input v-model="form.email" type="email" class="input" />
                            </label>
                            <label class="field">
                                <span class="label">Rol</span>
                                <input v-model="form.role" type="text" class="input" disabled />
                            </label>
                            <label class="field">
                                <span class="label">Miembro desde</span>
                                <input :value="joinedAt" type="text" class="input" disabled />
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-black text-white px-4 py-2 text-sm font-semibold hover:bg-black/85 cursor-pointer focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-teal-600 transition">
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
@reference "tailwindcss";

.card {
    @apply rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 shadow-sm w-full;
}

.field {
    @apply flex flex-col gap-1;
}

.label {
    @apply text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400;
}

.value {
    @apply text-base font-semibold text-zinc-900 dark:text-white;
}

.input {
    @apply w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 focus:border-teal-600 focus:ring-teal-600;
}
</style>
