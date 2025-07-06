<template>
    <Head title="Gestion des utilisateurs" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des utilisateurs</h2>
                <Link :href="route('admin.users.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Nouvel utilisateur
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filtres -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form @submit.prevent="search" class="flex flex-wrap gap-4">
                            <div class="flex-1 min-w-64">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="Rechercher par nom ou email..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div class="min-w-48">
                                <select
                                    v-model="form.role"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Tous les rôles</option>
                                    <option v-for="role in roles" :key="role" :value="role">
                                        {{ getRoleLabel(role) }}
                                    </option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Rechercher
                            </button>
                            <button
                                type="button"
                                @click="clearFilters"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                            >
                                Effacer
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Liste des utilisateurs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nom
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rôle
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date d'inscription
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in users.data" :key="user.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ user.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getRoleBadgeClass(user.role)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ getRoleLabel(user.role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <Link
                                                :href="route('admin.users.edit', user.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Modifier
                                            </Link>
                                            <button
                                                v-if="user.id !== $page.props.auth.user.id"
                                                @click="deleteUser(user)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Supprimer
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="users.prev_page_url"
                                    :href="users.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Précédent
                                </Link>
                                <Link
                                    v-if="users.next_page_url"
                                    :href="users.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Suivant
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Affichage de
                                        <span class="font-medium">{{ users.from }}</span>
                                        à
                                        <span class="font-medium">{{ users.to }}</span>
                                        sur
                                        <span class="font-medium">{{ users.total }}</span>
                                        résultats
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-if="users.prev_page_url"
                                            :href="users.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            Précédent
                                        </Link>
                                        <Link
                                            v-if="users.next_page_url"
                                            :href="users.next_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            Suivant
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    users: Object,
    filters: Object,
    roles: Array,
});

const form = reactive({
    search: props.filters.search || '',
    role: props.filters.role || '',
});

const search = () => {
    router.get(route('admin.users.index'), form, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    form.search = '';
    form.role = '';
    router.get(route('admin.users.index'), {}, {
        preserveState: true,
        replace: true,
    });
};

const deleteUser = (user) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur ${user.name} ?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

const getRoleLabel = (role) => {
    const labels = {
        'super_admin': 'Super Admin',
        'admin': 'Admin',
        'client': 'Client'
    };
    return labels[role] || role;
};

const getRoleBadgeClass = (role) => {
    const classes = {
        'super_admin': 'bg-red-100 text-red-800',
        'admin': 'bg-green-100 text-green-800',
        'client': 'bg-blue-100 text-blue-800'
    };
    return classes[role] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR');
};
</script>

