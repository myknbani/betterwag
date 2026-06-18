<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property CampaignType $type
 * @property CampaignStatus $status
 * @property-read Dog|null $dog
 * @property-read Shelter|null $shelter
 *
 * @method static \Database\Factories\CampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withoutTrashed()
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
