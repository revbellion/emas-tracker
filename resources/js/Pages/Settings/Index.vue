<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    providers: Array,
    latestBuyPrice: [Number, String],
    latestSellPrice: [Number, String],
    latestSyncInfo: String,
});

const flash = usePage().props.flash;

const parseIdr = (val) => {
    if (!val && val !== 0) return 0;
    return parseFloat(String(val).replace(/\./g, '').replace(',', '.')) || 0;
};

const onInput = (e) => {
    let val = e.target.value;
    val = val.replace(/[^0-9,.]/g, '');
    const dotCount = (val.match(/\./g) || []).length;
    const commaCount = (val.match(/,/g) || []).length;
    if (commaCount > 1) val = val.replace(/,/g, '');
    if (dotCount > 1 && commaCount === 0) val = val.replace(/\./g, '');
    e.target.value = val;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};

const form = useForm({
    buy_price: '',
    sell_price: '',
    price_date: '',
});

const submitForm = () => {
    form.buy_price = parseIdr(form.buy_price);
    form.sell_price = parseIdr(form.sell_price);
    if (!form.price_date) form.price_date = '';
    form.post(route('settings.manual-price'));
};
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Settings</h1>

                <!-- Flash Messages -->
                <div v-if="flash.success" class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <p class="text-sm text-green-700 dark:text-green-300">{{ flash.success }}</p>
                </div>
                <div v-if="flash.error" class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-sm text-red-700 dark:text-red-300">{{ flash.error }}</p>
                </div>

                <!-- Atur Harga Manual -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Atur Harga Manual</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Beli</label>
                            <input v-model="form.buy_price" @input="onInput" type="text" inputmode="numeric" placeholder="Contoh: 2.739.000"
                                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 block w-full" />
                            <p v-if="form.errors.buy_price" class="text-sm text-red-600 mt-1">{{ form.errors.buy_price }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Jual</label>
                            <input v-model="form.sell_price" @input="onInput" type="text" inputmode="numeric" placeholder="Contoh: 2.420.000"
                                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 block w-full" />
                            <p v-if="form.errors.sell_price" class="text-sm text-red-600 mt-1">{{ form.errors.sell_price }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal (opsional)</label>
                        <input v-model="form.price_date" type="date"
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 block w-full sm:w-64" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kosongi untuk menggunakan tanggal hari ini</p>
                    </div>

                    <button @click="submitForm" :disabled="form.processing"
                        class="inline-flex items-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-yellow-700 focus:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 active:bg-yellow-800 dark:focus:ring-offset-gray-800 disabled:opacity-50">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Harga Manual' }}
                    </button>
                </div>

                <!-- Harga Emas Terkini -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Harga Emas Terkini</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Harga Beli</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(latestBuyPrice) }}</p>
                        </div>
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Harga Jual</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(latestSellPrice) }}</p>
                        </div>
                    </div>

                    <div v-if="latestSyncInfo" class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Terakhir sinkron: <span class="font-medium">{{ latestSyncInfo }}</span>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <Link v-for="provider in providers" :key="provider"
                            :href="route('settings.sync-price', provider)"
                            method="post"
                            as="button"
                            preserve-scroll
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm transition-colors">
                            Sync {{ provider }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
