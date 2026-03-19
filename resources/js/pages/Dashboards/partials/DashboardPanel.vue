<template>
    <article :class="panelClasses">
        <div v-if="hasHeader" :class="headerClasses">
            <div>
                <p v-if="eyebrow" class="text-xs uppercase tracking-[0.18em] text-zinc-400">{{ eyebrow }}</p>
                <h2 v-if="title" class="mt-2 text-xl font-semibold text-zinc-950">{{ title }}</h2>
                <p v-if="description" class="mt-2 text-sm text-zinc-500">{{ description }}</p>
            </div>

            <slot name="headerAction">
                <div v-if="icon" :class="iconContainerClasses">
                    <component :is="icon" class="h-5 w-5" />
                </div>
            </slot>
        </div>

        <div :class="bodyClasses">
            <slot />
        </div>
    </article>
</template>

<script setup>
import { computed, useSlots } from 'vue';

const props = defineProps({
    eyebrow: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    icon: {
        type: [Object, Function],
        default: null,
    },
    panelClass: {
        type: String,
        default: '',
    },
    headerClass: {
        type: String,
        default: '',
    },
    bodyClass: {
        type: String,
        default: '',
    },
    iconClass: {
        type: String,
        default: '',
    },
});

const slots = useSlots();

const hasHeader = computed(() => Boolean(props.eyebrow || props.title || props.description || props.icon || slots.headerAction));

const panelClasses = computed(() => [
    'rounded-3xl bg-white p-6 shadow-[0_20px_55px_-36px_rgba(15,23,42,0.35)] ring-1 ring-zinc-200/80',
    props.panelClass,
]);

const headerClasses = computed(() => props.headerClass || 'flex items-start justify-between gap-4');

const iconContainerClasses = computed(() => [
    'rounded-2xl bg-zinc-50 p-3 text-zinc-700 ring-1 ring-zinc-200/80',
    props.iconClass,
]);

const bodyClasses = computed(() => props.bodyClass || (hasHeader.value ? 'mt-6' : ''));
</script>