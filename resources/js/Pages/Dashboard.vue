<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { formatDatetime } from '@/format.js';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Filler } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Filler);

const props = defineProps({
    overview: Object,
    recentTransactions: Array,
    portfolioGrowth: Array,
    daysParam: { default: 30 },
});

const presets = [
    { label: '1W', value: 7 },
    { label: '1M', value: 30 },
    { label: '3M', value: 90 },
    { label: '6M', value: 180 },
    { label: '1Y', value: 365 },
    { label: 'YTD', value: 'ytd' },
];

const changeDays = (value) => {
    router.get(route('dashboard'), { days: value }, { preserveState: true, replace: true });
};

const chartData = computed(() => ({
    labels: props.portfolioGrowth.map(p => p.date),
    datasets: [
        {
            label: 'Nilai Portofolio',
            data: props.portfolioGrowth.map(p => p.value),
            borderColor: '#CA8A04',
            backgroundColor: 'rgba(202, 138, 4, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#CA8A04',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (ctx) => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y),
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { color: '#9CA3AF', maxTicksLimit: 8 },
        },
        y: {
            grid: { color: 'rgba(156, 163, 175, 0.1)' },
            ticks: {
                color: '#9CA3AF',
                callback: (value) => 'Rp' + (value / 1000000).toFixed(0) + 'jt',
            },
        },
    },
}));

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('id-ID').format(value);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Dashboard</h1>

                <!-- Today's Gold Price -->
                <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Harga Emas Hari Ini</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Harga Beli</p>
                                <span v-if="overview.latest_buy_price > overview.prev_buy_price" class="text-green-600 dark:text-green-400">
                                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                </span>
                                <span v-else-if="overview.latest_buy_price < overview.prev_buy_price" class="text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                </span>
                                <span v-else class="text-gray-400">
                                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                                </span>
                            </div>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(overview.latest_buy_price) }}</p>
                        </div>
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Harga Jual</p>
                                <span v-if="overview.latest_sell_price > overview.prev_sell_price" class="text-green-600 dark:text-green-400">
                                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                </span>
                                <span v-else-if="overview.latest_sell_price < overview.prev_sell_price" class="text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                </span>
                                <span v-else class="text-gray-400">
                                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                                </span>
                            </div>
                            <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ formatCurrency(overview.latest_sell_price) }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Spread</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(overview.latest_buy_price - overview.latest_sell_price) }}</p>
                            <p v-if="overview.latest_buy_price > 0" class="text-xs text-gray-400 mt-1">{{ ((overview.latest_buy_price - overview.latest_sell_price) / overview.latest_buy_price * 100).toFixed(2) }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Overview Cards -->
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Emas</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ formatNumber(overview.total_gram) }} gram</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Modal</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ formatCurrency(overview.total_capital) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Current Value</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ formatCurrency(overview.current_value) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Profit/Loss</dt>
                                        <dd :class="overview.profit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'" class="text-lg font-semibold">
                                            {{ formatCurrency(overview.profit) }}
                                            <span class="text-sm">({{ overview.profit_percentage }}%)</span>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Portfolio Growth Chart -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Portfolio Growth</h3>
                            <div class="flex space-x-1">
                                    <button v-for="p in presets" :key="p.value" @click="changeDays(p.value)"
                                    :class="String(daysParam) === String(p.value) ? 'bg-yellow-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                    class="px-2.5 py-1 text-xs rounded-md font-medium">
                                    {{ p.label }}
                                </button>
                            </div>
                        </div>
                        <p v-if="portfolioGrowth.length > 0" class="text-xs text-gray-400 dark:text-gray-500 mb-4">{{ portfolioGrowth[0].date }} - {{ portfolioGrowth[portfolioGrowth.length - 1].date }}</p>
                        <div v-if="portfolioGrowth.length > 0" class="h-64">
                            <Line :data="chartData" :options="chartOptions" />
                        </div>
                        <div v-else class="h-64 flex items-center justify-center text-gray-400 dark:text-gray-500">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                <p class="mt-2 text-sm">Belum ada data harga emas</p>
                                <p class="text-xs">Input harga emas di menu Gold Prices</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Transactions</h3>
                            <a :href="route('transactions.index')" class="text-sm text-yellow-600 hover:text-yellow-500">View All</a>
                        </div>
                        <div class="flow-root">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                <li v-for="tx in recentTransactions" :key="tx.id" class="py-3">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ tx.member?.name }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                <span :class="{
                                                    'text-green-600 dark:text-green-400': tx.type === 'BUY' || tx.type === 'TRANSFER_IN',
                                                    'text-red-600 dark:text-red-400': tx.type === 'SELL' || tx.type === 'TRANSFER_OUT',
                                                    'text-yellow-600 dark:text-yellow-400': tx.type === 'ADJUSTMENT',
                                                }" class="font-medium">{{ tx.type.replace('_', ' ') }}</span>
                                                - {{ tx.gram }} gram
                                            </p>
                                        </div>
                                        <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDatetime(tx.transaction_date) }}
                                        </div>
                                    </div>
                                </li>
                                <li v-if="recentTransactions.length === 0" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                    No transactions yet
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
