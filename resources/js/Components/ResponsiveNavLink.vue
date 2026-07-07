<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: { type: String, required: true },
    active: { type: Boolean },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-amber-500 text-start text-base font-medium text-amber-700 dark:text-amber-300 bg-amber-50/50 dark:bg-amber-900/10 focus:outline-none focus:text-amber-800 dark:focus:text-amber-200 focus:bg-amber-100 dark:focus:bg-amber-900/20 focus:border-amber-700 transition duration-150 ease-in-out rounded-r-lg'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-amber-700 dark:hover:text-amber-300 hover:bg-amber-50/50 dark:hover:bg-gray-800 hover:border-amber-300 dark:hover:border-amber-700 focus:outline-none focus:text-amber-800 dark:focus:text-amber-200 focus:bg-amber-50 dark:focus:bg-gray-800 focus:border-amber-300 transition duration-150 ease-in-out rounded-r-lg'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
