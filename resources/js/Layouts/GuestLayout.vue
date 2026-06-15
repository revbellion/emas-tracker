<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-gray-100 dark:bg-gray-900 pt-6 sm:justify-center sm:pt-0">
        <div class="mb-6">
            <Link href="/">
                <ApplicationLogo class="h-16 w-16 fill-current text-yellow-600 dark:text-yellow-500" />
            </Link>
        </div>

        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gold Family Tracker</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pantau investasi emas keluarga</p>
        </div>

        <div class="w-full bg-white dark:bg-gray-800 px-6 py-6 shadow-sm sm:max-w-md sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <slot />
        </div>
    </div>
</template>
