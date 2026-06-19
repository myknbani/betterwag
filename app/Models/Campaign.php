<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use Carbon\CarbonImmutable;
use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property CampaignType $type
 * @property CampaignStatus $status
 * @property-read Dog|null $dog
 * @property-read Shelter|null $shelter
 * @property int $id
 * @property int $shelter_id
 * @property int|null $dog_id
 * @property string $title
 * @property string|null $description
 * @property int|null $goal_amount
 * @property CarbonImmutable|null $closed_at
 * @property string|null $closed_reason
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static \Database\Factories\CampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereClosedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereGoalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereShelterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereUpdatedAt($value)
 *
 * @property-read Collection<int, Donation> $donations
 * @property-read int|null $donations_count
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'shelter_id',
    'dog_id',
    'title',
    'description',
    'type',
    'status',
    'goal_amount',
    'closed_at',
    'closed_reason',
])]
class Campaign extends Model
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory;

    use SoftDeletes;

    /** @return BelongsTo<Shelter, $this> */
    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    /** @return BelongsTo<Dog, $this> */
    public function dog(): BelongsTo
    {
        return $this->belongsTo(Dog::class);
    }

    /** @return HasMany<Donation, $this> */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function close(?string $reason = null): void
    {
        $this->update([
            'status' => CampaignStatus::Closed,
            'closed_at' => now(),
            'closed_reason' => $reason,
        ]);
    }

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
            'goal_amount' => 'integer',
            'type' => CampaignType::class,
            'status' => CampaignStatus::class,
        ];
    }
}
