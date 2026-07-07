<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
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
});

const applyFilters = () => {
    router.get(route('transactions.index'), filters, { preserveState: true, replace: true });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};

const voidForm = useForm({});
const voidTransaction = (tx) => {
    if (confirm('Void this transaction?')) {
        voidForm.delete(route('transactions.void', tx.id));
    }
};
</script>

<template>
    <Head title="Transactions" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-wrap justify-between items-center gap-3">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Transactions</h1>
                    <Link :href="route('transactions.create')" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm">+ New Transaction</Link>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mt-6 mb-6 flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                        <input v-model="filters.search" type="text" placeholder="Cari transaksi..." @keyup.enter="applyFilters" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:border-yellow-500 focus:ring-yellow-500 w-48" />
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
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gram</th>
                                <th class="hidden sm:table-cell px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price/Gram</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button @click="voidTransaction(tx)" class="text-red-600 hover:text-red-500">Void</button>
                                </td>
                            </tr>
                            <tr v-if="transactions.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No transactions yet</td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="transactions.links && transactions.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap justify-between items-center gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Page {{ transactions.current_page }} of {{ transactions.last_page }}</span>
                        <div class="flex flex-wrap gap-1">
                            <a v-for="link in transactions.links" :key="link.label" :href="link.url || '#'" v-html="link.label" class="px-3 py-1 text-sm rounded" :class="link.active ? 'bg-yellow-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
