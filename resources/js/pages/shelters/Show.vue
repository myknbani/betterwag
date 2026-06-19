<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Shelter {
    id: number;
    name: string;
    location: string | null;
    description: string | null;
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
    dogs: { data: Dog[] };
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
    </div>
</template>
