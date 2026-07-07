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
    datasets: [{
        label: 'Nilai Portofolio',
        data: props.portfolioGrowth.map(p => p.value),
        borderColor: '#D97706',
        backgroundColor: (ctx) => {
            if (!ctx.chart.chartArea) return 'rgba(217, 119, 6, 0.08)';
            const grad = ctx.chart.ctx.createLinearGradient(0, ctx.chart.chartArea.top, 0, ctx.chart.chartArea.bottom);
            grad.addColorStop(0, 'rgba(217, 119, 6, 0.25)');
            grad.addColorStop(1, 'rgba(217, 119, 6, 0.01)');
            return grad;
        },
        borderWidth: 2.5,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#D97706',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 3,
        pointHoverRadius: 6,
        pointHoverBackgroundColor: '#D97706',
    }],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1a1a1a',
            titleColor: '#fff',
            bodyColor: '#FDE68A',
            cornerRadius: 8,
            padding: 10,
            callbacks: {
                label: (ctx) => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y),
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { color: '#9CA3AF', maxTicksLimit: 8, font: { size: 11 } },
        },
        y: {
            grid: { color: 'rgba(156, 163, 175, 0.12)' },
            ticks: {
                color: '#9CA3AF',
                font: { size: 11 },
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
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Overview portfolio emas keluarga</p>
                </div>
            </div>

            <!-- Today's Gold Price -->
            <div class="bg-white/70 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-sm border border-amber-200/30 dark:border-gray-800 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Harga Emas Hari Ini</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/10 dark:to-yellow-900/10 rounded-xl border border-amber-200/50 dark:border-amber-800/30">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide">Harga Beli</p>
                            <span v-if="overview.latest_buy_price > overview.prev_buy_price" class="text-emerald-600 dark:text-emerald-400"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg></span>
                            <span v-else-if="overview.latest_buy_price < overview.prev_buy_price" class="text-red-600 dark:text-red-400"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg></span>
                            <span v-else class="text-gray-400"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14"/></svg></span>
                        </div>
                        <p class="text-2xl font-bold text-amber-700 dark:text-amber-300">{{ formatCurrency(overview.latest_buy_price) }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/10 dark:to-yellow-900/10 rounded-xl border border-amber-200/50 dark:border-amber-800/30">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide">Harga Jual</p>
                            <span v-if="overview.latest_sell_price > overview.prev_sell_price" class="text-emerald-600 dark:text-emerald-400"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg></span>
                            <span v-else-if="overview.latest_sell_price < overview.prev_sell_price" class="text-red-600 dark:text-red-400"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg></span>
                            <span v-else class="text-gray-400"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14"/></svg></span>
                        </div>
                        <p class="text-2xl font-bold text-amber-700 dark:text-amber-300">{{ formatCurrency(overview.latest_sell_price) }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-gray-50 to-amber-50 dark:from-gray-800/50 dark:to-gray-800/30 rounded-xl border border-gray-200/50 dark:border-gray-700/50">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Spread</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(overview.latest_buy_price - overview.latest_sell_price) }}</p>
                        <p v-if="overview.latest_buy_price > 0" class="text-xs text-gray-400 mt-0.5">{{ ((overview.latest_buy_price - overview.latest_sell_price) / overview.latest_buy_price * 100).toFixed(2) }}%</p>
                    </div>
                </div>
            </div>

            <!-- Overview Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="card in [
                    { label: 'Total Emas', value: formatNumber(overview.total_gram) + ' gram', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'from-amber-500 to-yellow-500' },
                    { label: 'Total Modal', value: formatCurrency(overview.total_capital), icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', color: 'from-blue-500 to-sky-500' },
                    { label: 'Current Value', value: formatCurrency(overview.current_value), icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', color: 'from-emerald-500 to-green-500' },
                    { label: 'Profit/Loss', value: formatCurrency(overview.profit), icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', color: overview.profit >= 0 ? 'from-emerald-500 to-green-500' : 'from-red-500 to-rose-500' },
                ]" :key="card.label"
                    class="bg-white/70 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-sm border border-amber-200/20 dark:border-gray-800 overflow-hidden group hover:shadow-md hover:shadow-amber-200/10 transition-all duration-300">
                    <div class="p-5">
                        <div class="flex items-center gap-4">
                            <div :class="`bg-gradient-to-br ${card.color} p-3 rounded-xl shadow-lg`">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ card.label }}</p>
                                <p v-if="card.label === 'Profit/Loss'" :class="overview.profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'" class="text-lg font-bold mt-0.5">
                                    {{ card.value }}
                                    <span class="text-sm font-normal">({{ overview.profit_percentage }}%)</span>
                                </p>
                                <p v-else class="text-lg font-bold text-gray-900 dark:text-white mt-0.5 truncate">{{ card.value }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart & Recent Transactions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Portfolio Growth Chart -->
                <div class="bg-white/70 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-sm border border-amber-200/20 dark:border-gray-800 p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pertumbuhan Portofolio</h3>
                        </div>
                        <div class="flex gap-1">
                            <button v-for="p in presets" :key="p.value" @click="changeDays(p.value)"
                                :class="String(daysParam) === String(p.value) ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-amber-100 dark:hover:bg-gray-700'"
                                class="px-2.5 py-1 text-xs font-medium rounded-lg transition-all duration-200">
                                {{ p.label }}
                            </button>
                        </div>
                    </div>
                    <p v-if="portfolioGrowth.length > 0" class="text-xs text-gray-400 mb-3">{{ portfolioGrowth[0].date }} — {{ portfolioGrowth[portfolioGrowth.length - 1].date }}</p>
                    <div v-if="portfolioGrowth.length > 0" class="h-64">
                        <Line :data="chartData" :options="chartOptions" />
                    </div>
                    <div v-else class="h-64 flex items-center justify-center text-gray-400">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <p class="mt-2 text-sm font-medium">Belum ada data harga emas</p>
                            <p class="text-xs text-gray-400">Input harga emas di menu Settings</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white/70 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-sm border border-amber-200/20 dark:border-gray-800 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Transaksi Terbaru</h3>
                        </div>
                        <a :href="route('transactions.index')" class="text-xs font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300 transition-colors">Lihat Semua →</a>
                    </div>
                    <div class="flow-root">
                        <ul class="divide-y divide-amber-100/50 dark:divide-gray-700/50">
                            <li v-for="tx in recentTransactions" :key="tx.id" class="py-3 first:pt-0 last:pb-0">
                                <div class="flex items-center gap-3">
                                    <div :class="tx.type === 'BUY' || tx.type === 'TRANSFER_IN' ? 'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : tx.type === 'SELL' || tx.type === 'TRANSFER_OUT' ? 'bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'bg-amber-100 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400'"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path v-if="tx.type === 'BUY'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            <path v-else-if="tx.type === 'SELL'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ tx.member?.name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            <span :class="{'text-emerald-600 dark:text-emerald-400': tx.type === 'BUY' || tx.type === 'TRANSFER_IN', 'text-red-600 dark:text-red-400': tx.type === 'SELL' || tx.type === 'TRANSFER_OUT', 'text-amber-600 dark:text-amber-400': tx.type === 'ADJUSTMENT'}" class="font-medium">{{ tx.type.replace('_', ' ') }}</span>
                                            · {{ tx.gram }} gram
                                        </p>
                                    </div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap text-right">
                                        {{ formatDatetime(tx.transaction_date) }}
                                    </div>
                                </div>
                            </li>
                            <li v-if="recentTransactions.length === 0" class="py-8 text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="mt-2 text-sm text-gray-400">Belum ada transaksi</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
