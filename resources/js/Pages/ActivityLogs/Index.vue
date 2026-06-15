<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    logs: Object,
});
</script>

<template>
    <Head title="Activity Logs" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Activity Logs</h1>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="log in logs.data" :key="log.id" class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center">
                                        <span class="text-sm font-medium text-yellow-600 dark:text-yellow-300">{{ log.user?.name?.charAt(0) || 'S' }}</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        <span class="font-medium">{{ log.user?.name || 'System' }}</span>
                                        {{ log.description }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ log.created_at }}</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">{{ log.action }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="logs.data.length === 0" class="p-12 text-center text-gray-500 dark:text-gray-400">
                            No activity logs yet
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
