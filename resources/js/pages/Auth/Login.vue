<template>
    <div class="min-h-screen w-full flex items-center justify-center bg-teal-900/10 dark:bg-zinc-950 px-4 py-8">
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 bg-white dark:bg-zinc-900 rounded-[28px] shadow-2xl overflow-hidden ring-1 ring-zinc-200/70 dark:ring-zinc-800">
            <!-- Left: Form -->
            <div class="p-8 sm:p-10 lg:p-12">
                <!-- Brand + Toggle -->
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">VentaCarros</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Explora más. Vive mejor.</span>
                    </div>
                    <div class="inline-flex gap-3">
                        <button type="button" @click="isRegister = true" :class="isRegister ? 'bg-black text-white' : 'border border-zinc-300 dark:border-zinc-700 text-gray-900 dark:text-gray-100 hover:bg-zinc-50 dark:hover:bg-zinc-800'" class="px-4 py-2 rounded-lg text-sm transition-colors cursor-pointer hover:opacity-80">Sign Up</button>
                        <button type="button" @click="isRegister = false" :class="!isRegister ? 'bg-black text-white' : 'border border-zinc-300 dark:border-zinc-700 text-gray-900 dark:text-gray-100 hover:bg-zinc-50 dark:hover:bg-zinc-800'" class="px-4 py-2 rounded-lg text-sm transition-colors cursor-pointer hover:opacity-80">Log In</button>
                    </div>
                </div>

                <!-- Headline -->
                <h2 class="mt-10 text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ isRegister ? 'Create Account' : 'Journey Begins' }}</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ isRegister ? 'Sign up to get started' : 'Log in with Open account' }}</p>

                <!-- Social buttons -->
                <div class="mt-4 grid grid-cols-3 gap-3">
                    <a href="/auth/apple/redirect" aria-label="Sign in with Apple" class="h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-current text-gray-900 dark:text-gray-100"><path d="M16.365 1.43c0 1.14-.463 2.238-1.23 3.04-.774.81-2.058 1.44-3.14 1.35-.14-1.11.41-2.28 1.18-3.09.78-.83 2.15-1.45 3.19-1.3zM20.42 17.49c-.59 1.36-.87 1.97-1.63 3.18-1.06 1.72-2.56 3.86-4.41 3.88-1.65.02-2.08-1.12-4.34-1.11-2.27.01-2.73 1.13-4.39 1.11-1.86-.02-3.28-1.97-4.34-3.69C-.14 17.87-.8 14.3.78 11.9c1.02-1.5 2.64-2.39 4.48-2.41 1.76-.03 3.42 1.19 4.33 1.19.9 0 2.7-1.48 4.55-1.26.77.03 2.93.31 4.32 2.32-3.44 1.86-2.89 6.57 0 7.83z"/></svg>
                    </a>
                    <a href="/auth/google/redirect" aria-label="Sign in with Google" class="h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12 s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C33.206,6.053,28.791,4,24,4C12.955,4,4,11.955,4,23 s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,16.108,18.961,13,24,13c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657 C33.206,6.053,28.791,4,24,4C16.318,4,9.656,7.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c4.738,0,9.053-1.807,12.317-4.756l-5.682-4.807C28.503,35.188,26.345,36,24,36 c-5.202,0-9.616-3.317-11.276-7.946l-6.49,5.047C9.484,39.556,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.089,5.572 c0.001-0.001,0.002-0.001,0.003-0.002l6.49,5.493C36.49,39.25,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
                    </a>
                    <a href="/auth/github/redirect" aria-label="Sign in with GitHub" class="h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-current text-gray-900 dark:text-gray-100"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                </div>

                <!-- Divider -->
                <div class="my-6 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-800"></div>
                    <span>or</span>
                    <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>

                <!-- Login/Register form -->
                <form class="space-y-4" @submit.prevent="handleSubmit">
                    <div v-if="isRegister">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Name</label>
                        <input v-model="form.name" type="text" required
                                     class="mt-1 w-full rounded-lg border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary px-3 py-2" />
                        <p v-if="authStore.errors.name" class="mt-1 text-xs text-red-600">{{ authStore.errors.name[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Email</label>
                        <input v-model="form.email" type="email" autocomplete="email" required
                                     class="mt-1 w-full rounded-lg border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary px-3 py-2" />
                        <p v-if="authStore.errors.email" class="mt-1 text-xs text-red-600">{{ authStore.errors.email[0] }}</p>
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Password</label>
                            <a v-if="!isRegister" href="#" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400">Forgot Password?</a>
                        </div>
                        <div class="relative mt-1">
                            <input v-model="form.password" type="password" autocomplete="current-password" required
                                         class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary px-3 py-2" />
                            <p v-if="authStore.errors.password" class="mt-1 text-xs text-red-600">{{ authStore.errors.password[0] }}</p>
                        </div>
                    </div>
                    <div v-if="isRegister">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-200">Confirm Password</label>
                        <input v-model="form.password_confirmation" type="password" required
                                     class="mt-1 w-full rounded-lg border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:border-primary focus:ring-primary px-3 py-2" />
                    </div>
                    <div v-if="!isRegister" class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <input type="checkbox" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" />
                            Remember me
                        </label>
                    </div>

                    <button type="submit" :disabled="authStore.loading"
                                    class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-black px-4 py-2 text-white font-medium hover:opacity-90 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-black disabled:opacity-50 cursor-pointer transition-all">
                        {{ authStore.loading ? (isRegister ? 'Creating account…' : 'Logging in…') : (isRegister ? 'Create Account' : 'Log In') }}
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
import { useToast } from "vue-toastification";

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
            toast.success("Account created successfully!")
        } else {
            await authStore.login({ email: form.email, password: form.password })
            toast.success("Welcome back!")
        }
    } catch (error) {
        toast.error("Authentication failed. Please check your credentials.")
    }
}
</script>
