<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    backups: Array,
    flash: Object,
});

const formatSize = (bytes) => {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
};

const download = () => {
    window.location.href = route('backups.download');
};

const restore = () => {
    const input = document.getElementById('restore-file');
    if (!input.files.length) return;
    if (!confirm('Yakin ingin restore database? Semua data saat ini akan diganti dengan data dari file backup.')) return;

    const form = new FormData();
    form.append('backup_file', input.files[0]);

    router.post(route('backups.restore'), form, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => { input.value = ''; },
        onError: () => { input.value = ''; },
    });
};
</script>

<template>
    <Head title="Backup & Restore" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Backup &amp; Restore</h1>
                    <button @click="download"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm font-medium">
                        <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Backup Sekarang
                    </button>
                </div>

                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success"
                    class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-300">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error"
                    class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-300">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Restore Form -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Restore Database</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Pilih file backup (.sql) untuk mengembalikan database. 
                        <strong class="text-red-500">Peringatan:</strong> Semua data saat ini akan ditimpa!
                    </p>
                    <div class="flex flex-wrap items-center gap-3">
                        <input id="restore-file" type="file" accept=".sql,.txt"
                            class="block w-full sm:w-auto text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-yellow-50 dark:file:bg-yellow-900/20 file:text-yellow-700 dark:file:text-yellow-300 hover:file:bg-yellow-100 dark:hover:file:bg-yellow-900/40 cursor-pointer" />
                        <button @click="restore"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                            Restore
                        </button>
                    </div>
                </div>

                <!-- Backup List -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Riwayat Backup</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                    <th class="hidden sm:table-cell px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="b in backups" :key="b.filename"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ b.filename }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                        {{ formatSize(b.size) }}
                                    </td>
                                    <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                        {{ b.date }}
                                    </td>
                                </tr>
                                <tr v-if="backups.length === 0">
                                    <td colspan="3" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                        Belum ada file backup. Klik "Backup Sekarang" untuk membuat backup pertama.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
