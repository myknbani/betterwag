<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import AppLogo from '@/components/AppLogo.vue';
import { login } from '@/routes';

const auth = usePage().props.auth;
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <header class="border-b border-sidebar-border/80">
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4"
            >
                <Link href="/" class="flex items-center">
                    <AppLogo />
                </Link>
                <Link
                    v-if="!auth.user"
                    :href="login()"
                    class="text-sm font-medium hover:underline"
                >
                    Login
                </Link>
                <Link
                    v-else
                    href="/dashboard"
                    class="text-sm font-medium hover:underline"
                >
                    Dashboard
                </Link>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6">
            <slot />
        </main>
    </div>
</template>
