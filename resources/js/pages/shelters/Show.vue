<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

interface Shelter {
    id: number;
    name: string;
    location: string | null;
    description: string | null;
}

interface Campaign {
    id: number;
    title: string;
    description: string | null;
    goalAmount: number | null;
    collectedAmount: number;
    dogId: number | null;
    type: string;
}

interface Dog {
    id: number;
    name: string;
    breed: string | null;
    ageMonths: number | null;
    gender: 'male' | 'female' | null;
    adoptionStatus: string;
    isUrgent: boolean;
    photoUrl: string;
}

const props = defineProps<{
    shelter: Shelter;
    campaigns: { data: Campaign[] };
    dogs: {
        data: Dog[];
        links: { prev: string | null; next: string | null };
        meta: { current_page: number; last_page: number; total: number };
    };
}>();

function formatAge(months: number | null): string {
    if (!months) {
        return 'Unknown age';
    }

    if (months < 12) {
        return `${months}mo`;
    }

    const years = Math.floor(months / 12);
    const rem = months % 12;

    return rem > 0 ? `${years}y ${rem}mo` : `${years}y`;
}
</script>

<template>
    <Head :title="props.shelter.name" />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <h1 class="text-2xl font-bold">{{ props.shelter.name }}</h1>
            <p
                v-if="props.shelter.location"
                class="text-sm text-muted-foreground"
            >
                {{ props.shelter.location }}
            </p>
            <p v-if="props.shelter.description" class="mt-2 text-sm">
                {{ props.shelter.description }}
            </p>
        </div>

        <div v-if="props.campaigns.data.length" class="flex flex-col gap-3">
            <h2 class="text-lg font-semibold">Active Campaigns</h2>
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="campaign in props.campaigns.data"
                    :key="campaign.id"
                    class="flex flex-col gap-2 rounded-xl border border-sidebar-border/70 bg-card p-4"
                >
                    <div class="flex items-start justify-between gap-2">
                        <span class="font-semibold">{{ campaign.title }}</span>
                        <span
                            class="shrink-0 rounded-full bg-secondary px-2 py-0.5 text-xs capitalize"
                        >
                            {{ campaign.type }}
                        </span>
                    </div>
                    <p
                        v-if="campaign.description"
                        class="line-clamp-2 text-xs text-muted-foreground"
                    >
                        {{ campaign.description }}
                    </p>
                    <div class="mt-1 flex items-center justify-between text-sm">
                        <span class="text-muted-foreground">
                            ${{
                                (
                                    campaign.collectedAmount / 100
                                ).toLocaleString()
                            }}
                            raised
                        </span>
                        <span v-if="campaign.goalAmount" class="font-medium">
                            of ${{
                                (campaign.goalAmount / 100).toLocaleString()
                            }}
                        </span>
                    </div>
                    <div
                        v-if="campaign.goalAmount"
                        class="h-1.5 w-full overflow-hidden rounded-full bg-secondary"
                    >
                        <div
                            class="h-full rounded-full bg-primary transition-all"
                            :style="{
                                width: `${Math.min((campaign.collectedAmount / campaign.goalAmount) * 100, 100)}%`,
                            }"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            <div
                v-for="dog in props.dogs.data"
                :key="dog.id"
                class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border"
            >
                <div class="relative aspect-square">
                    <img
                        :src="dog.photoUrl"
                        :alt="dog.name"
                        class="h-full w-full object-cover"
                    />
                    <span
                        v-if="dog.isUrgent"
                        class="absolute top-2 right-2 rounded-full bg-red-500 px-2 py-0.5 text-xs font-semibold text-white"
                    >
                        Urgent
                    </span>
                </div>

                <div class="p-3">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold">{{ dog.name }}</span>
                        <span
                            class="text-xs text-muted-foreground capitalize"
                            >{{ dog.gender ?? '—' }}</span
                        >
                    </div>
                    <p class="text-xs text-muted-foreground">
                        {{ dog.breed ?? 'Mixed' }}
                    </p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-muted-foreground">{{
                            formatAge(dog.ageMonths)
                        }}</span>
                        <span
                            class="rounded-full bg-secondary px-2 py-0.5 text-xs capitalize"
                        >
                            {{ dog.adoptionStatus.replace('_', ' ') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <span class="text-sm text-muted-foreground">
                Page {{ props.dogs.meta.current_page }} of
                {{ props.dogs.meta.last_page }} ({{ props.dogs.meta.total }}
                dogs)
            </span>
            <div class="flex gap-2">
                <Link
                    v-if="props.dogs.links.prev"
                    :href="props.dogs.links.prev"
                    class="rounded-lg border border-sidebar-border/70 px-3 py-1.5 text-sm hover:bg-secondary"
                >
                    Previous
                </Link>
                <Link
                    v-if="props.dogs.links.next"
                    :href="props.dogs.links.next"
                    class="rounded-lg border border-sidebar-border/70 px-3 py-1.5 text-sm hover:bg-secondary"
                >
                    Next
                </Link>
            </div>
        </div>
    </div>
</template>
