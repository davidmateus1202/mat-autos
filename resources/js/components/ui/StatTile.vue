<template>
    <div class="card p-5">
        <div class="flex items-center">
            <div class="shrink-0">
                <slot name="icon">
                    <!-- Default Icon Placeholder -->
                    <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </slot>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="truncate text-sm font-medium text-muted dark:text-muted-dark">
                        {{ title }}
                    </dt>
                    <dd class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">
                        {{ value }}
                    </dd>
                </dl>
            </div>
        </div>
        <div v-if="trend" class="mt-4">
            <div 
                class="text-sm"
                :class="trend > 0 ? 'text-success' : 'text-danger'"
            >
                <span class="font-medium">{{ trend > 0 ? '+' : '' }}{{ trend }}%</span>
                <span class="text-muted dark:text-muted-dark ml-1">from last month</span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    value: [String, Number],
    trend: Number, // Optional percentage change
});
</script>
