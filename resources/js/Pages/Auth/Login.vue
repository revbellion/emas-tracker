<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
    errors: Object,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />
    <GuestLayout>
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Selamat Datang</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Silakan login ke akun Anda</p>
        </div>

        <div v-if="status" class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg text-sm text-emerald-700 dark:text-emerald-300">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username" placeholder="nama@email.com" />
                <InputError :message="errors?.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />
                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" placeholder="Masukkan password" />
                <InputError :message="errors?.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="form.remember" class="rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-amber-600 focus:ring-2 focus:ring-amber-400/30 transition-all" />
                    <span class="text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
                </label>
                <a v-if="canResetPassword" :href="route('password.request')" class="text-sm font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300 underline-offset-2 hover:underline transition-colors">
                    Lupa password?
                </a>
            </div>

            <button type="submit" :disabled="form.processing"
                class="w-full py-2.5 bg-gradient-to-r from-amber-600 to-yellow-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-amber-200/30 dark:shadow-amber-900/20 hover:from-amber-700 hover:to-yellow-600 hover:shadow-xl transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed">
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 inline" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                <span v-else>Masuk</span>
            </button>
        </form>
    </GuestLayout>
</template>
