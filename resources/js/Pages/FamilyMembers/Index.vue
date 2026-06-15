<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    members: Object,
});

const showForm = ref(false);
const editingMember = ref(null);

const form = useForm({
    name: '',
    relationship: '',
    notes: '',
    is_active: true,
});

const openCreateForm = () => {
    editingMember.value = null;
    form.reset();
    showForm.value = true;
};

const openEditForm = (member) => {
    editingMember.value = member;
    form.name = member.name;
    form.relationship = member.relationship || '';
    form.notes = member.notes || '';
    form.is_active = member.is_active;
    showForm.value = true;
};

const submitForm = () => {
    if (editingMember.value) {
        form.patch(route('family-members.update', editingMember.value.id), {
            onSuccess: () => { showForm.value = false; form.reset(); }
        });
    } else {
        form.post(route('family-members.store'), {
            onSuccess: () => { showForm.value = false; form.reset(); }
        });
    }
};

const deactivateMember = (member) => {
    if (confirm(`Nonaktifkan ${member.name}?`)) {
        form.delete(route('family-members.deactivate', member.id));
    }
};
</script>

<template>
    <Head title="Family Members" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Family Members</h1>
                    <button @click="openCreateForm" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">+ Add Member</button>
                </div>

                <!-- Form Modal -->
                <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showForm = false">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ editingMember ? 'Edit Member' : 'Add Member' }}</h3>
                        <form @submit.prevent="submitForm">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                    <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                                    <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Relationship</label>
                                    <input v-model="form.relationship" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                    <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" @click="showForm = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900">Cancel</button>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm">{{ editingMember ? 'Update' : 'Save' }}</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Members Table -->
                <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Relationship</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Transactions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="member in members.data" :key="member.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Link :href="route('family-members.show', member.id)" class="text-sm font-medium text-gray-900 dark:text-white hover:text-yellow-600">{{ member.name }}</Link>
                                </td>
                                <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ member.relationship || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ member.transactions_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="member.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300'" class="px-2 py-1 text-xs rounded-full">{{ member.is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button @click="openEditForm(member)" class="text-yellow-600 hover:text-yellow-500 mr-3">Edit</button>
                                    <button @click="deactivateMember(member)" v-if="member.is_active" class="text-red-600 hover:text-red-500">Deactivate</button>
                                </td>
                            </tr>
                            <tr v-if="members.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No family members yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
