<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { loadStripe } from '@stripe/stripe-js';
import type { Stripe, StripeCardElement } from '@stripe/stripe-js';
import { ref, watch, nextTick } from 'vue';
import { login } from '@/routes';

interface Campaign {
    id: number;
    title: string;
}

const props = defineProps<{
    open: boolean;
    campaign: Campaign | null;
}>();

const emit = defineEmits<{
    close: [];
    success: [amount: number];
}>();

const page = usePage();
const auth = page.props.auth as { user: { id: number } | null };
const stripeKey = page.props.stripeKey as string;

const PRESET_AMOUNTS = [500, 1000, 2500, 5000];
const selectedPreset = ref(1000);
const customAmount = ref('');
const cardHolderName = ref('');
const errorMessage = ref('');
const isLoading = ref(false);
const isSuccess = ref(false);

let stripe: Stripe | null = null;
let cardElement: StripeCardElement | null = null;
const cardRef = ref<HTMLDivElement | null>(null);

watch(
    () => props.open,
    async (isOpen) => {
        if (!isOpen) {
            cardElement?.unmount();
            cardElement = null;

            return;
        }

        errorMessage.value = '';
        isSuccess.value = false;
        isLoading.value = false;
        selectedPreset.value = 1000;
        customAmount.value = '';
        cardHolderName.value = '';

        if (!auth.user) {
            return;
        }

        await nextTick();

        if (!stripe) {
            stripe = await loadStripe(stripeKey);
        }

        if (stripe && cardRef.value) {
            const isDark = document.documentElement.classList.contains('dark');
            const elements = stripe.elements();
            cardElement = elements.create('card', {
                style: {
                    base: {
                        color: isDark ? '#f9fafb' : '#111827',
                        fontSize: '14px',
                        fontFamily: 'inherit',
                        '::placeholder': {
                            color: isDark ? '#6b7280' : '#9ca3af',
                        },
                    },
                    invalid: { color: '#ef4444' },
                },
            });
            cardElement.mount(cardRef.value);
        }
    },
);

function getAmountCents(): number {
    if (customAmount.value) {
        return Math.round(parseFloat(customAmount.value) * 100);
    }

    return selectedPreset.value;
}

function getCsrfToken(): string {
    const cookie = document.cookie
        .split('; ')
        .find((r) => r.startsWith('XSRF-TOKEN='));

    return decodeURIComponent(cookie?.split('=')[1] ?? '');
}

async function submit() {
    if (!stripe || !cardElement || !props.campaign) {
        return;
    }

    isLoading.value = true;
    errorMessage.value = '';

    const { paymentMethod, error: pmError } = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: { name: cardHolderName.value || undefined },
    });

    if (pmError || !paymentMethod) {
        errorMessage.value = pmError?.message ?? 'Could not process card.';
        isLoading.value = false;

        return;
    }

    const response = await fetch(`/api/campaigns/${props.campaign.id}/donate`, {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-XSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({
            type: 'one_time',
            amount: getAmountCents(),
            payment_method_id: paymentMethod.id,
        }),
    });

    const json = await response.json();

    if (!response.ok) {
        errorMessage.value =
            json.message ?? 'Payment failed. Please try again.';
        isLoading.value = false;

        return;
    }

    if (json.clientSecret) {
        const { error: confirmError } = await stripe.confirmCardPayment(
            json.clientSecret,
        );

        if (confirmError) {
            errorMessage.value =
                confirmError.message ?? 'Payment confirmation failed.';
            isLoading.value = false;

            return;
        }
    }

    emit('success', getAmountCents());
    isSuccess.value = true;
    isLoading.value = false;
}

function donateLabel(): string {
    const dollars = customAmount.value
        ? parseFloat(customAmount.value)
        : selectedPreset.value / 100;

    return `Donate $${dollars}`;
}
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
            @click.self="emit('close')"
        >
            <div
                class="w-full max-w-md rounded-2xl bg-background p-6 shadow-xl"
            >
                <!-- Success -->
                <div
                    v-if="isSuccess"
                    class="flex flex-col items-center gap-4 py-4 text-center"
                >
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-2xl text-green-600"
                    >
                        ✓
                    </div>
                    <h2 class="text-lg font-semibold">Thank you!</h2>
                    <p class="text-sm text-muted-foreground">
                        Your donation to
                        <span class="font-medium">{{ campaign?.title }}</span>
                        is being processed.
                    </p>
                    <button
                        class="mt-2 rounded-lg bg-primary px-6 py-2 text-sm font-medium text-primary-foreground"
                        @click="emit('close')"
                    >
                        Done
                    </button>
                </div>

                <!-- Guest prompt -->
                <div
                    v-else-if="!auth.user"
                    class="flex flex-col items-center gap-4 py-6 text-center"
                >
                    <p class="text-sm text-muted-foreground">
                        You need an account to donate.
                    </p>
                    <Link
                        :href="login()"
                        class="rounded-lg bg-primary px-6 py-2 text-sm font-medium text-primary-foreground"
                    >
                        Login
                    </Link>
                    <button
                        class="text-xs text-muted-foreground hover:underline"
                        @click="emit('close')"
                    >
                        Cancel
                    </button>
                </div>

                <!-- Donation form -->
                <template v-else>
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold">
                            Donate to {{ campaign?.title }}
                        </h2>
                        <button
                            class="text-muted-foreground hover:text-foreground"
                            @click="emit('close')"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div>
                            <p class="mb-2 text-sm font-medium">
                                Select amount
                            </p>
                            <div class="grid grid-cols-4 gap-2">
                                <button
                                    v-for="cents in PRESET_AMOUNTS"
                                    :key="cents"
                                    class="rounded-lg border py-2 text-sm transition-colors"
                                    :class="
                                        selectedPreset === cents &&
                                        !customAmount
                                            ? 'border-primary bg-primary text-primary-foreground'
                                            : 'border-sidebar-border/70 hover:bg-secondary'
                                    "
                                    @click="
                                        selectedPreset = cents;
                                        customAmount = '';
                                    "
                                >
                                    ${{ cents / 100 }}
                                </button>
                            </div>
                        </div>

                        <input
                            v-model="customAmount"
                            type="number"
                            min="1"
                            step="1"
                            placeholder="Custom amount (USD)"
                            class="w-full rounded-lg border border-sidebar-border/70 bg-background px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-primary"
                        />

                        <input
                            v-model="cardHolderName"
                            type="text"
                            placeholder="Name on card"
                            class="w-full rounded-lg border border-sidebar-border/70 bg-background px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-primary"
                        />

                        <div
                            class="rounded-lg border border-sidebar-border/70 px-3 py-3"
                        >
                            <div ref="cardRef" />
                        </div>

                        <p v-if="errorMessage" class="text-sm text-red-500">
                            {{ errorMessage }}
                        </p>

                        <button
                            class="rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground disabled:opacity-50"
                            :disabled="isLoading"
                            @click="submit"
                        >
                            {{ isLoading ? 'Processing...' : donateLabel() }}
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </Teleport>
</template>
