<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const showingNavigationDropdown = ref(false);
const sidebarCollapsed = ref(false);

const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Family Members', href: route('family-members.index'), icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
    { name: 'Transactions', href: route('transactions.index'), icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
    { name: 'Reports', href: route('reports.portfolio'), icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Activity Logs', href: route('activity-logs.index'), icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Settings', href: route('settings.index'), icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
    { name: 'Backups', href: route('backups.index'), icon: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12' },
];

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};

// Helper to get route pattern for nav items
const getRoutePattern = (item) => {
    const map = {
        'Dashboard': 'dashboard',
        'Family Members': 'family-members.*',
        'Transactions': 'transactions.*',
        'Reports': 'reports.*',
        'Activity Logs': 'activity-logs.*',
        'Settings': 'settings.*',
        'Backups': 'backups.*',
    };
    return map[item.name] || '';
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-yellow-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
        <!-- Navbar -->
        <nav class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b border-amber-200/50 dark:border-gray-800 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <button @click="toggleSidebar"
                            class="hidden sm:inline-flex mr-3 p-2 rounded-lg text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-200 hover:bg-amber-100 dark:hover:bg-gray-800 transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <Link :href="route('dashboard')" class="flex items-center gap-2.5">
                            <ApplicationLogo class="block h-9 w-9" />
                            <span class="hidden sm:block text-lg font-bold bg-gradient-to-r from-amber-600 via-yellow-500 to-amber-600 bg-clip-text text-transparent">Emas Tracker</span>
                        </Link>
                        <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button type="button"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 border border-amber-200/60 dark:border-gray-700 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white/60 dark:bg-gray-800/60 hover:bg-amber-50 dark:hover:bg-gray-800 focus:outline-none transition-all duration-200">
                                            <span class="w-6 h-6 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-xs font-bold">{{ $page.props.auth.user.name.charAt(0) }}</span>
                                            <span class="hidden lg:inline">{{ $page.props.auth.user.name }}</span>
                                            <svg class="h-4 w-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="p-2 rounded-lg text-amber-600 hover:text-amber-800 hover:bg-amber-100 dark:text-amber-400 dark:hover:text-amber-200 dark:hover:bg-gray-800 transition-all duration-200">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }" class="sm:hidden border-t border-amber-100 dark:border-gray-800">
                <div class="pt-2 pb-3 space-y-1 px-3">
                    <ResponsiveNavLink v-for="item in navigation" :key="item.name" :href="item.href" :active="route().current(getRoutePattern(item))">
                        <template #default>
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                </svg>
                                {{ item.name }}
                            </div>
                        </template>
                    </ResponsiveNavLink>
                </div>
                <div class="pt-4 pb-3 border-t border-amber-100 dark:border-gray-800 px-4">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-sm font-bold">{{ $page.props.auth.user.name.charAt(0) }}</span>
                        <div>
                            <div class="font-medium text-sm text-gray-900 dark:text-gray-200">{{ $page.props.auth.user.name }}</div>
                            <div class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Sidebar -->
            <aside :class="sidebarCollapsed ? 'w-16' : 'w-64'"
                class="bg-gradient-to-b from-white to-amber-50/50 dark:from-gray-900 dark:to-gray-900 border-r border-amber-200/40 dark:border-gray-800 transition-all duration-300 hidden sm:block min-h-[calc(100vh-4rem)] sticky top-16">
                <nav class="mt-4 px-2 space-y-0.5">
                    <Link v-for="item in navigation" :key="item.name" :href="item.href"
                        :class="[
                            route().current(getRoutePattern(item))
                                ? 'bg-gradient-to-r from-amber-100 to-amber-50 dark:from-amber-900/30 dark:to-amber-800/10 text-amber-800 dark:text-amber-300 border-l-2 border-amber-500'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-amber-50/50 dark:hover:bg-gray-800/50 hover:text-amber-700 dark:hover:text-amber-300 border-l-2 border-transparent',
                            'group flex items-center px-3 py-2.5 text-sm font-medium transition-all duration-200 rounded-r-lg'
                        ]">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        <span v-show="!sidebarCollapsed" class="truncate">{{ item.name }}</span>
                    </Link>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 min-h-[calc(100vh-4rem)]">
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
