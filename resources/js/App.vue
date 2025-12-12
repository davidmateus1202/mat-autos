<template>
    <component :is="useLayout ? DefaultLayout : 'div'">
        <router-view />
    </component>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import DefaultLayout from './components/layout/DefaultLayout.vue'

const route = useRoute()
const useLayout = computed(() => route.meta.layout !== 'none')
</script>
