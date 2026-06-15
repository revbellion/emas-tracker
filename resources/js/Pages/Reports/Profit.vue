<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    data: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Profit Report" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Profit Report</h1>
                    <a :href="route('reports.profit.export')" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm rounded-md hover:bg-yellow-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Excel
                    </a>
                </div>

                <!-- Summary -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Gram</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ data.summary.total_gram }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Capital</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(data.summary.total_capital) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Value</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(data.summary.total_value) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Profit</p>
                        <p :class="data.summary.total_profit >= 0 ? 'text-green-600' : 'text-red-600'" class="text-xl font-bold">{{ formatCurrency(data.summary.total_profit) }} ({{ data.summary.total_profit_percentage }}%)</p>
                    </div>
                </div>

                <!-- Detail Table -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gram</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Capital</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Current Value</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                                <th class="hidden sm:table-cell px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="item in data.details" :key="item.member_name">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ item.member_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ item.gram }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ formatCurrency(item.capital) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ formatCurrency(item.current_value) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right" :class="item.profit >= 0 ? 'text-green-600' : 'text-red-600'">{{ formatCurrency(item.profit) }}</td>
                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-right" :class="item.profit >= 0 ? 'text-green-600' : 'text-red-600'">{{ item.profit_percentage }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
