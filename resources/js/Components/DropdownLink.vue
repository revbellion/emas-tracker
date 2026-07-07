<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: { type: String, required: true },
});
</script>

<template>
    <Link :href="href"
        class="block w-full px-4 py-2.5 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/10 hover:text-amber-700 dark:hover:text-amber-300 focus:outline-none focus:bg-amber-50 dark:focus:bg-amber-900/10 focus:text-amber-700 dark:focus:text-amber-300 transition-all duration-150">
        <slot />
    </Link>
</template>
