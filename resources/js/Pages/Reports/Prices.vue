<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    prices: Object,
});

const filters = reactive({
    type: route().params.type || '',
    from: route().params.from || '',
    to: route().params.to || '',
    sort: route().params.sort || 'price_date',
    direction: route().params.direction || 'desc',
});

const applyFilters = () => {
    router.get(route('reports.prices'), filters, { preserveState: true, replace: true });
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
    filters.type = '';
    filters.from = '';
    filters.to = '';
    filters.sort = 'price_date';
    filters.direction = 'desc';
    applyFilters();
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Gold Price History" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Gold Price History</h1>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm mb-6 flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Type</label>
                        <select v-model="filters.type" @change="applyFilters" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="">All</option>
                            <option value="BUY">Buy</option>
                            <option value="SELL">Sell</option>
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
                                <th @click="sortBy('price_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-yellow-600 dark:hover:text-yellow-400 select-none">Date{{ sortIcon('price_date') }}</th>
                                <th @click="sortBy('type')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-yellow-600 dark:hover:text-yellow-400 select-none">Type{{ sortIcon('type') }}</th>
                                <th @click="sortBy('price')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-yellow-600 dark:hover:text-yellow-400 select-none">Price{{ sortIcon('price') }}</th>
                                <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="price in prices.data" :key="price.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ price.price_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap"><span :class="price.type === 'BUY' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300'" class="px-2 py-1 text-xs rounded-full">{{ price.type }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-white">{{ formatCurrency(price.price) }}</td>
                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ price.provider || '-' }}</td>
                            </tr>
                            <tr v-if="prices.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No prices found</td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="prices.links && prices.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap justify-between items-center gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Page {{ prices.current_page }} of {{ prices.last_page }}</span>
                        <div class="flex flex-wrap gap-1">
                            <a v-for="link in prices.links" :key="link.label" :href="link.url || '#'" v-html="link.label" class="px-3 py-1 text-sm rounded" :class="link.active ? 'bg-yellow-600 text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
