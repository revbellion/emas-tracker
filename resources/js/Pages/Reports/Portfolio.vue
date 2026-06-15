<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    portfolio: Array,
    members: Array,
});

const selectedMember = ref(route().params.member_id || '');

const filterByMember = () => {
    router.get(route('reports.portfolio'), { member_id: selectedMember.value || '' }, { preserveState: true, replace: true });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Portfolio Report" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Portfolio Report</h1>
                    <a :href="route('reports.portfolio.export', { member_id: selectedMember })" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm rounded-md hover:bg-yellow-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Excel
                    </a>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mb-6 flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Member</label>
                        <select v-model="selectedMember" @change="filterByMember" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="">All Members</option>
                            <option v-for="m in members" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gold Balance (gram)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Capital</th>
                                <th class="hidden sm:table-cell px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Price / Gram</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Current Value</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit/Loss</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="item in portfolio" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ item.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ item.gold_balance }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ formatCurrency(item.total_capital) }}</td>
                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-white">{{ formatCurrency(item.average_price) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ formatCurrency(item.current_value) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right" :class="item.profit >= 0 ? 'text-green-600' : 'text-red-600'">{{ formatCurrency(item.profit) }}</td>
                            </tr>
                            <tr v-if="portfolio.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No portfolio data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
