<?php

namespace App\Models;

use App\Enums\DonationStatus;
use App\Enums\DonationType;
use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'campaign_id',
    'user_id',
    'type',
    'amount',
    'status',
    'stripe_payment_intent_id',
    'stripe_subscription_id',
    'paid_at',
])]
class Donation extends Model
{
    /** @use HasFactory<DonationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => DonationType::class,
            'status' => DonationStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Campaign, $this> */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
