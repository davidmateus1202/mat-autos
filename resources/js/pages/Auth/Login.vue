<template>
    <div class="min-h-screen w-full flex items-center justify-center bg-teal-900/10 dark:bg-zinc-950 px-4 py-8">
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 bg-white dark:bg-zinc-900 rounded-[28px] shadow-2xl overflow-hidden ring-1 ring-zinc-200/70 dark:ring-zinc-800">
            <!-- Left: Form -->
            <div class="relative p-8 sm:p-10 lg:p-12">
                <LoadingOverlay :visible="authStore.loading" />

                <!-- Brand + Toggle -->
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">VentaCarros</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Explora más. Vive mejor.</span>
                    </div>
                    <div class="inline-flex gap-3">
                        <button type="button" @click="isRegister = true" :class="isRegister ? 'bg-black text-white' : 'border border-zinc-300 dark:border-zinc-700 text-gray-900 dark:text-gray-100 hover:bg-zinc-50 dark:hover:bg-zinc-800'" class="px-4 py-2 rounded-lg text-sm transition-colors cursor-pointer hover:opacity-80">Crear cuenta</button>
                        <button type="button" @click="isRegister = false" :class="!isRegister ? 'bg-black text-white' : 'border border-zinc-300 dark:border-zinc-700 text-gray-900 dark:text-gray-100 hover:bg-zinc-50 dark:hover:bg-zinc-800'" class="px-4 py-2 rounded-lg text-sm transition-colors cursor-pointer hover:opacity-80">Iniciar sesión</button>
                    </div>
                </div>

                <!-- Headline -->
                <h2 class="mt-10 text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ isRegister ? 'Crea tu cuenta' : 'Tu viaje comienza aquí' }}</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ isRegister ? 'Regístrate para empezar' : 'Ingresa con tu cuenta activa' }}</p>

                <!-- Social buttons -->
                <div class="mt-4 grid grid-cols-3 gap-3">
                    <a href="/auth/apple/redirect" aria-label="Continuar con Apple" class="h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center justify-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        <DevicePhoneMobileIcon class="h-5 w-5" />
                        <span>Apple</span>
                    </a>
                    <a href="/auth/google/redirect" aria-label="Continuar con Google" class="h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center justify-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        <GlobeAltIcon class="h-5 w-5" />
                        <span>Google</span>
                    </a>
                    <a href="/auth/github/redirect" aria-label="Continuar con GitHub" class="h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center justify-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        <CodeBracketIcon class="h-5 w-5" />
                        <span>GitHub</span>
                    </a>
                </div>

                <!-- Divider -->
                <div class="my-6 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-800"></div>
                    <span>o</span>
                    <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>

                <!-- Login/Register form -->
                <form class="space-y-4" @submit.prevent="handleSubmit">
                    <div v-if="isRegister">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Nombre completo</label>
                        <input v-model="form.name" type="text" required
                                     class="mt-1 w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary px-3 py-2" />
                        <p v-if="authStore.errors.name" class="mt-1 text-xs text-red-600">{{ authStore.errors.name[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Correo electrónico</label>
                        <input v-model="form.email" type="email" autocomplete="email" required
                                     class="mt-1 w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary px-3 py-2" />
                        <p v-if="authStore.errors.email" class="mt-1 text-xs text-red-600">{{ authStore.errors.email[0] }}</p>
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Contraseña</label>
                            <a v-if="!isRegister" href="#" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400">¿Olvidaste tu contraseña?</a>
                        </div>
                        <div class="relative mt-1">
                            <input v-model="form.password" type="password" autocomplete="current-password" required
                                         class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary px-3 py-2" />
                            <p v-if="authStore.errors.password" class="mt-1 text-xs text-red-600">{{ authStore.errors.password[0] }}</p>
                        </div>
                    </div>
                    <div v-if="isRegister">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Confirmar contraseña</label>
                        <input v-model="form.password_confirmation" type="password" required
                                     class="mt-1 w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary px-3 py-2" />
                    </div>
                    <div v-if="!isRegister" class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <input type="checkbox" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" />
                            Recuérdame
                        </label>
                    </div>

                    <button type="submit" :disabled="authStore.loading" :aria-busy="authStore.loading"
                                    class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-black px-4 py-2 text-white font-medium hover:opacity-90 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-black disabled:opacity-50 cursor-pointer transition-all">
                        {{ isRegister ? 'Crear cuenta' : 'Iniciar sesión' }}
                    </button>
                </form>
            </div>

            <!-- Right: Car image panel -->
            <div class="relative hidden lg:block">
                <div class="absolute inset-0" :style="{ backgroundImage: `url('${heroUrl}')`, backgroundSize: 'cover', backgroundPosition: 'center' }"></div>
                <div class="absolute inset-0 bg-black/20"></div>

                <!-- Overlay badge/text -->
                <div class="absolute top-6 right-6 bg-white/95 dark:bg-zinc-900/95 text-gray-900 dark:text-white rounded-xl shadow px-4 py-3 ring-1 ring-zinc-200/70 dark:ring-zinc-800 max-w-xs">
                    <p class="text-sm font-semibold">Conduce, Explora, Disfruta.</p>
                    <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Descubre tu próximo carro con ofertas imperdibles.</p>
                </div>

                <!-- Bottom CTA -->
                <div class="absolute bottom-8 left-8 right-8 flex items-center justify-between">
                    <p class="text-white/95 text-lg font-semibold drop-shadow">Encuentra tu próximo carro hoy</p>
                    <button type="button" class="rounded-full bg-white/90 text-gray-900 text-sm px-4 py-2 hover:bg-white">Explorar catálogo</button>
                </div>

                <!-- Decorative rounded corner approximation -->
                <div class="absolute bottom-0 right-0 w-12 h-12 bg-white dark:bg-zinc-900 rounded-tl-[28px]"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, computed, ref } from 'vue'
import { useAuthStore } from '../../stores/useAuth'
import { useToast } from "vue-toastification"
import { DevicePhoneMobileIcon, GlobeAltIcon, CodeBracketIcon } from '@heroicons/vue/24/outline'
import LoadingOverlay from '../../components/ui/LoadingOverlay.vue'

const authStore = useAuthStore()
const toast = useToast()
const isRegister = ref(false)
const form = reactive({ 
    name: '',
    email: '', 
    password: '',
    password_confirmation: ''
})

// Allow overriding via Vite env: VITE_LOGIN_IMAGE_URL
const heroUrl = computed(() => {
    const envUrl = import.meta.env.VITE_LOGIN_IMAGE_URL
    // Car-themed Unsplash image as default
    return envUrl || 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1600&auto=format&fit=crop'
})

const handleSubmit = async () => {
    try {
        if (isRegister.value) {
            await authStore.register(form)
            toast.success("¡Cuenta creada con éxito!")
        } else {
            await authStore.login({ email: form.email, password: form.password })
            toast.success("¡Bienvenido de nuevo!")
        }
    } catch (error) {
        toast.error("No se pudo autenticar. Verifica tus credenciales.")
    }
}
</script>
