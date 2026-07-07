<script setup>
defineProps({
    type: { type: String, default: 'button' },
});
</script>

<template>
    <button :type="type"
        class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 shadow-sm transition-all duration-200 hover:bg-amber-50 dark:hover:bg-gray-700 hover:text-amber-700 dark:hover:text-amber-300 hover:border-amber-300 dark:hover:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:opacity-60 disabled:cursor-not-allowed">
        <slot />
    </button>
</template>
