<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { User, LogOut } from 'lucide-vue-next';
import { logout } from '@/routes';

const handleLogout = () => {
    logout();
};
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton size="lg" class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
                        <UserInfo :user="user" />
                        <ChevronsUpDown class="ml-auto size-4" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                    :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'"
                    align="end"
                    :side-offset="4"
                >
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
    <div class="relative group">
        <button 
            class="flex items-center space-x-2 text-gray-600 hover:text-blue-600 font-medium transition-colors duration-200"
        >
            <User class="h-5 w-5" />
            <span>{{ $page.props.auth.user.name }}</span>
        </button>
        <!-- Dropdown menu -->
        <div class="absolute right-0 mt-2 w-48 py-2 bg-white rounded-lg shadow-xl dark:bg-gray-800 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
            <Link
                href="/profile"
                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            >
                Profile
            </Link>
            <button
                @click="handleLogout"
                class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            >
                <div class="flex items-center space-x-2">
                    <LogOut class="h-4 w-4" />
                    <span>Logout</span>
                </div>
            </button>
        </div>
    </div>
</template>
