<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { formatDatetime } from '@/format.js';

const props = defineProps({
    member: Object,
    transactions: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head :title="member.name" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <div class="mb-6">
                    <Link :href="route('family-members.index')" class="text-sm text-yellow-600 hover:text-yellow-500">&larr; Back to Members</Link>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ member.name }}</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">{{ member.relationship || '-' }}</p>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gold Balance</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ member.gold_balance }} gram</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Capital</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(member.total_capital) }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                            <p class="text-xl font-bold" :class="member.is_active ? 'text-green-600' : 'text-red-600'">{{ member.is_active ? 'Active' : 'Inactive' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-x-auto">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Transaction History</h2>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gram</th>
                                <th class="hidden sm:table-cell px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price/Gram</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="tx in transactions.data" :key="tx.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDatetime(tx.transaction_date) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="{
                                        'bg-green-100 text-green-800': tx.type === 'BUY' || tx.type === 'TRANSFER_IN',
                                        'bg-red-100 text-red-800': tx.type === 'SELL' || tx.type === 'TRANSFER_OUT',
                                        'bg-yellow-100 text-yellow-800': tx.type === 'ADJUSTMENT',
                                    }" class="px-2 py-1 text-xs rounded-full">{{ tx.type.replace('_', ' ') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right" :class="tx.gram > 0 ? 'text-green-600' : 'text-red-600'">{{ tx.gram > 0 ? '+' : '' }}{{ tx.gram }}</td>
                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ formatCurrency(tx.price_per_gram) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ formatCurrency(tx.total_amount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
