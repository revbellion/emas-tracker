<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { formatDatetime } from '@/format.js';

const props = defineProps({
    transactions: Object,
    members: Array,
});

const filters = reactive({
    search: route().params.search || '',
    member_id: route().params.member_id || '',
    type: route().params.type || '',
    from: route().params.from || '',
    to: route().params.to || '',
    sort: route().params.sort || 'transaction_date',
    direction: route().params.direction || 'desc',
});

const applyFilters = () => {
    router.get(route('reports.transactions'), filters, { preserveState: true, replace: true });
};

const sortBy = (field) => {
    if (filters.sort === field) {
        filters.direction = filters.direction === 'asc' ? 'desc' : 'asc';
    } else {
        filters.sort = field;
        filters.direction = 'asc';
    }
    applyFilters();
};

const sortIcon = (field) => {
    if (filters.sort !== field) return '';
    return filters.direction === 'asc' ? ' ▲' : ' ▼';
};

const resetFilters = () => {
    filters.search = '';
    filters.member_id = '';
    filters.type = '';
    filters.from = '';
    filters.to = '';
    filters.sort = 'transaction_date';
    filters.direction = 'desc';
    applyFilters();
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Transaction Report" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Transaction Report</h1>
                    <a :href="route('reports.transactions.export', filters)" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm rounded-md hover:bg-yellow-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Excel
                    </a>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mb-6 flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                        <input v-model="filters.search" type="text" placeholder="Cari deskripsi/anggota..." @keyup.enter="applyFilters" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:border-yellow-500 focus:ring-yellow-500 w-48" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Member</label>
                        <select v-model="filters.member_id" @change="applyFilters" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:border-yellow-500 focus:ring-yellow-500 w-36">
                            <option value="">All</option>
                            <option v-for="m in members" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Type</label>
                        <select v-model="filters.type" @change="applyFilters" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:border-yellow-500 focus:ring-yellow-500 w-28">
                            <option value="">All</option>
                            <option value="BUY">Buy</option>
                            <option value="SELL">Sell</option>
                            <option value="TRANSFER_IN">Transfer In</option>
                            <option value="TRANSFER_OUT">Transfer Out</option>
                            <option value="ADJUSTMENT">Adjustment</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">From</label>
                        <input v-model="filters.from" type="date" @change="applyFilters" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">To</label>
                        <input v-model="filters.to" type="date" @change="applyFilters" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                    </div>
                    <button @click="resetFilters" class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">Reset</button>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th @click="sortBy('transaction_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-yellow-600 dark:hover:text-yellow-400 select-none">Date{{ sortIcon('transaction_date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th @click="sortBy('type')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-yellow-600 dark:hover:text-yellow-400 select-none">Type{{ sortIcon('type') }}</th>
                                <th @click="sortBy('gram')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-yellow-600 dark:hover:text-yellow-400 select-none">Gram{{ sortIcon('gram') }}</th>
                                <th class="hidden sm:table-cell px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price/Gram</th>
                                <th @click="sortBy('total_amount')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-yellow-600 dark:hover:text-yellow-400 select-none">Total{{ sortIcon('total_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="tx in transactions.data" :key="tx.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDatetime(tx.transaction_date) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ tx.member?.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300': tx.type === 'BUY' || tx.type === 'TRANSFER_IN',
                                        'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300': tx.type === 'SELL' || tx.type === 'TRANSFER_OUT',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300': tx.type === 'ADJUSTMENT',
                                    }" class="px-2 py-1 text-xs rounded-full">{{ tx.type.replace('_', ' ') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right" :class="tx.gram > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">{{ tx.gram > 0 ? '+' : '' }}{{ tx.gram }}</td>
                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ formatCurrency(tx.price_per_gram) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ formatCurrency(tx.total_amount) }}</td>
                            </tr>
                            <tr v-if="transactions.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No transactions found</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="transactions.links && transactions.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Page {{ transactions.current_page }} of {{ transactions.last_page }}</span>
                        <div class="flex gap-1">
                            <a v-for="link in transactions.links" :key="link.label" :href="link.url || '#'" v-html="link.label" class="px-3 py-1 text-sm rounded" :class="link.active ? 'bg-yellow-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
