<template>
    <article class="rounded-[26px] bg-white p-5 shadow-[0_18px_45px_-36px_rgba(15,23,42,0.45)] ring-1 ring-zinc-200/80">
        <div class="flex items-start justify-between gap-4">
            <div :class="['inline-flex h-12 w-12 items-center justify-center rounded-2xl ring-1', toneClass]">
                <component :is="icon" class="h-6 w-6" />
            </div>
            <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-[11px] font-medium text-zinc-500">{{ badge }}</span>
        </div>

        <p class="mt-5 text-sm text-zinc-500">{{ title }}</p>
        <p class="mt-2 text-2xl font-semibold tracking-tight text-zinc-950">{{ value }}</p>

        <div class="mt-4 space-y-1 text-sm">
            <p class="font-medium text-zinc-700">{{ helper }}</p>
            <p class="text-zinc-400">{{ subtext }}</p>
        </div>
    </article>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: '',
    },
    value: {
        type: [String, Number],
        default: '',
    },
    helper: {
        type: String,
        default: '',
    },
    subtext: {
        type: String,
        default: '',
    },
    badge: {
        type: String,
        default: '',
    },
    icon: {
        type: [Object, Function],
        default: null,
    },
    toneClass: {
        type: String,
        default: '',
    },
});
</script>