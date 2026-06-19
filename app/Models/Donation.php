<?php

namespace App\Models;

use App\Enums\DonationStatus;
use App\Enums\DonationType;
use Carbon\CarbonImmutable;
use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property DonationStatus $status
 * @property DonationType $type
 * @property int $id
 * @property int $campaign_id
 * @property int $user_id
 * @property int $amount
 * @property string|null $stripe_payment_intent_id
 * @property string|null $stripe_subscription_id
 * @property CarbonImmutable|null $paid_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Campaign|null $campaign
 * @property-read User $user
 *
 * @method static \Database\Factories\DonationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereStripePaymentIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereStripeSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereUserId($value)
 *
 * @mixin \Eloquent
 */
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
