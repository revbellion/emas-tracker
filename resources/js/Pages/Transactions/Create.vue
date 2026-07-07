<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    members: Array,
    latestBuyPrice: Number,
    latestSellPrice: Number,
});

const parseIdr = (val) => {
    if (!val && val !== 0) return 0;
    return parseFloat(String(val).replace(/\./g, '').replace(',', '.')) || 0;
};

const formatIdr = (val) => {
    const n = parseIdr(val);
    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 4 }).format(n);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
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

const transactionType = ref('buy');
const buyInputMode = ref('gram');
const sellInputMode = ref('gram');

const now = new Date();
const defaultDatetime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);

const buyForm = useForm({ member_id: '', gram: '', price_per_gram: props.latestBuyPrice, transaction_date: defaultDatetime, description: '' });
const sellForm = useForm({ member_id: '', gram: '', price_per_gram: props.latestSellPrice, transaction_date: defaultDatetime, description: '' });
const transferForm = useForm({ from_member_id: '', to_member_id: '', gram: '', price_per_gram: 0, transaction_date: defaultDatetime, description: '' });
const adjustmentForm = useForm({ member_id: '', gram: '', price_per_gram: 0, transaction_date: defaultDatetime, description: '' });

const buyTotalRupiah = ref('');
const sellTotalRupiah = ref('');

const updateBuyGramFromRupiah = () => {
    const ppg = parseIdr(buyForm.price_per_gram);
    if (ppg > 0) {
        buyForm.gram = (parseIdr(buyTotalRupiah.value) / ppg).toFixed(4);
    }
};
const updateBuyRupiahFromGram = () => {
    buyTotalRupiah.value = formatIdr(parseIdr(buyForm.gram) * parseIdr(buyForm.price_per_gram));
};
const updateBuyFromPrice = () => {
    if (buyInputMode.value === 'gram') updateBuyRupiahFromGram();
    else updateBuyGramFromRupiah();
};
const updateSellGramFromRupiah = () => {
    const ppg = parseIdr(sellForm.price_per_gram);
    if (ppg > 0) {
        sellForm.gram = (parseIdr(sellTotalRupiah.value) / ppg).toFixed(4);
    }
};
const updateSellRupiahFromGram = () => {
    sellTotalRupiah.value = formatIdr(parseIdr(sellForm.gram) * parseIdr(sellForm.price_per_gram));
};
const updateSellFromPrice = () => {
    if (sellInputMode.value === 'gram') updateSellRupiahFromGram();
    else updateSellGramFromRupiah();
};

const normalizeForm = (form) => {
    if (form.gram) form.gram = parseIdr(form.gram);
    if (form.price_per_gram) form.price_per_gram = parseIdr(form.price_per_gram);
};

const submitForm = () => {
    switch (transactionType.value) {
        case 'buy': normalizeForm(buyForm); buyForm.post(route('transactions.buy')); break;
        case 'sell': normalizeForm(sellForm); sellForm.post(route('transactions.sell')); break;
        case 'transfer': normalizeForm(transferForm); transferForm.post(route('transactions.transfer')); break;
        case 'adjustment': normalizeForm(adjustmentForm); adjustmentForm.post(route('transactions.adjustment')); break;
    }
};
</script>

<template>
    <Head title="New Transaction" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-3xl mx-auto">
                <div class="mb-6">
                    <Link :href="route('transactions.index')" class="text-sm text-yellow-600 hover:text-yellow-500">&larr; Back to Transactions</Link>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">New Transaction</h1>

                    <div class="flex flex-wrap gap-2 mb-6">
                        <button v-for="type in ['buy', 'sell', 'transfer', 'adjustment']" :key="type" @click="transactionType = type"
                            :class="transactionType === type ? 'bg-yellow-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            class="px-4 py-2 rounded-md text-sm font-medium capitalize">
                            {{ type }}
                        </button>
                    </div>

                    <form @submit.prevent="submitForm">
                        <!-- Buy Form -->
                        <div v-if="transactionType === 'buy'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Member</label>
                                <select v-model="buyForm.member_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <option value="">Select member</option>
                                    <option v-for="m in members" :key="m.id" :value="m.id">{{ m.name }}</option>
                                </select>
                                <p v-if="buyForm.errors.member_id" class="text-red-500 text-sm mt-1">{{ buyForm.errors.member_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Input Mode</label>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="buyInputMode = 'gram'; buyForm.gram = ''; buyTotalRupiah = ''" :class="buyInputMode === 'gram' ? 'bg-yellow-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-3 py-1.5 text-xs rounded-md">By Gram</button>
                                    <button type="button" @click="buyInputMode = 'rupiah'; buyTotalRupiah = ''; buyForm.gram = ''" :class="buyInputMode === 'rupiah' ? 'bg-yellow-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-3 py-1.5 text-xs rounded-md">By Rupiah</button>
                                </div>
                            </div>
                            <div v-if="buyInputMode === 'gram'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gram</label>
                                <input v-model="buyForm.gram" @input="onInput($event); updateBuyRupiahFromGram()" type="text" inputmode="decimal" placeholder="Contoh: 0,5 atau 1.25" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                                <p v-if="buyForm.errors.gram" class="text-red-500 text-sm mt-1">{{ buyForm.errors.gram }}</p>
                            </div>
                            <div v-else>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Rupiah (Rp)</label>
                                <input v-model="buyTotalRupiah" @input="onInput($event); updateBuyGramFromRupiah()" type="text" inputmode="numeric" placeholder="Contoh: 500.000 atau 500000" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price per Gram (Rp)</label>
                                <input v-model="buyForm.price_per_gram" @input="onInput($event); updateBuyFromPrice()" type="text" inputmode="numeric" placeholder="Contoh: 2.500.000" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                                <p v-if="buyForm.errors.price_per_gram" class="text-red-500 text-sm mt-1">{{ buyForm.errors.price_per_gram }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                <input v-model="buyForm.transaction_date" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea v-model="buyForm.description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                            </div>
                            <div class="pt-4">
                                <p v-if="buyInputMode === 'gram'" class="text-sm text-gray-500">Total: <span class="font-semibold">{{ formatCurrency(parseIdr(buyForm.gram) * parseIdr(buyForm.price_per_gram)) }}</span></p>
                                <p v-else class="text-sm text-gray-500">Gram yang didapat: <span class="font-semibold">{{ buyForm.gram || '0' }}</span></p>
                            </div>
                        </div>

                        <!-- Sell Form -->
                        <div v-if="transactionType === 'sell'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Member</label>
                                <select v-model="sellForm.member_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <option value="">Select member</option>
                                    <option v-for="m in members" :key="m.id" :value="m.id">{{ m.name }}</option>
                                </select>
                                <p v-if="sellForm.errors.member_id" class="text-red-500 text-sm mt-1">{{ sellForm.errors.member_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Input Mode</label>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="sellInputMode = 'gram'; sellForm.gram = ''; sellTotalRupiah = ''" :class="sellInputMode === 'gram' ? 'bg-yellow-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-3 py-1.5 text-xs rounded-md">By Gram</button>
                                    <button type="button" @click="sellInputMode = 'rupiah'; sellTotalRupiah = ''; sellForm.gram = ''" :class="sellInputMode === 'rupiah' ? 'bg-yellow-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-3 py-1.5 text-xs rounded-md">By Rupiah</button>
                                </div>
                            </div>
                            <div v-if="sellInputMode === 'gram'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gram</label>
                                <input v-model="sellForm.gram" @input="onInput($event); updateSellRupiahFromGram()" type="text" inputmode="decimal" placeholder="Contoh: 0,5 atau 1.25" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                                <p v-if="sellForm.errors.gram" class="text-red-500 text-sm mt-1">{{ sellForm.errors.gram }}</p>
                            </div>
                            <div v-else>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Rupiah (Rp)</label>
                                <input v-model="sellTotalRupiah" @input="onInput($event); updateSellGramFromRupiah()" type="text" inputmode="numeric" placeholder="Contoh: 500.000 atau 500000" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price per Gram (Rp)</label>
                                <input v-model="sellForm.price_per_gram" @input="onInput($event); updateSellFromPrice()" type="text" inputmode="numeric" placeholder="Contoh: 2.500.000" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                <input v-model="sellForm.transaction_date" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea v-model="sellForm.description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                            </div>
                            <div class="pt-4">
                                <p v-if="sellInputMode === 'gram'" class="text-sm text-gray-500">Total: <span class="font-semibold">{{ formatCurrency(parseIdr(sellForm.gram) * parseIdr(sellForm.price_per_gram)) }}</span></p>
                                <p v-else class="text-sm text-gray-500">Gram yang didapat: <span class="font-semibold">{{ sellForm.gram || '0' }}</span></p>
                            </div>
                        </div>

                        <!-- Transfer Form -->
                        <div v-if="transactionType === 'transfer'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Member</label>
                                <select v-model="transferForm.from_member_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <option value="">Select member</option>
                                    <option v-for="m in members" :key="m.id" :value="m.id">{{ m.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Member</label>
                                <select v-model="transferForm.to_member_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <option value="">Select member</option>
                                    <option v-for="m in members" :key="m.id" :value="m.id">{{ m.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gram</label>
                                <input v-model="transferForm.gram" @input="onInput" type="text" inputmode="decimal" placeholder="Contoh: 0,5 atau 1.25" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                <input v-model="transferForm.transaction_date" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea v-model="transferForm.description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                            </div>
                        </div>

                        <!-- Adjustment Form -->
                        <div v-if="transactionType === 'adjustment'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Member</label>
                                <select v-model="adjustmentForm.member_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <option value="">Select member</option>
                                    <option v-for="m in members" :key="m.id" :value="m.id">{{ m.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gram (+/-)</label>
                                <input v-model="adjustmentForm.gram" @input="onInput" type="text" inputmode="decimal" placeholder="Contoh: 0,5 atau -1.25" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price per Gram (Rp)</label>
                                <input v-model="adjustmentForm.price_per_gram" @input="onInput" type="text" inputmode="numeric" placeholder="Contoh: 2.500.000" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                <input v-model="adjustmentForm.transaction_date" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea v-model="adjustmentForm.description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" :disabled="buyForm.processing || sellForm.processing || transferForm.processing || adjustmentForm.processing"
                                class="px-6 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 disabled:opacity-50">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
