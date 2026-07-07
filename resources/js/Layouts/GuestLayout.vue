<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-yellow-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 flex flex-col items-center justify-center px-4 py-12">
        <!-- Decorative circles -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-amber-200/30 dark:bg-amber-900/10 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-yellow-200/30 dark:bg-yellow-900/10 blur-3xl"></div>
        </div>

        <div class="relative">
            <div class="mb-8 flex justify-center">
                <Link href="/">
                    <div class="flex items-center gap-3">
                        <ApplicationLogo class="h-12 w-12" />
                        <span class="text-2xl font-bold bg-gradient-to-r from-amber-600 via-yellow-500 to-amber-600 bg-clip-text text-transparent">Emas Tracker</span>
                    </div>
                </Link>
            </div>

            <div class="w-full max-w-md bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl px-8 py-8 shadow-xl shadow-amber-200/20 dark:shadow-black/20 rounded-2xl border border-amber-200/50 dark:border-gray-800">
                <slot />
            </div>

            <p class="mt-6 text-center text-xs text-gray-400 dark:text-gray-600">
                &copy; {{ new Date().getFullYear() }} Emas Tracker. All rights reserved.
            </p>
        </div>
    </div>
</template>
