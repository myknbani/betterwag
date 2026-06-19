<?php

namespace App\Models;

use App\Enums\AdoptionStatus;
use App\Enums\Gender;
use Carbon\CarbonImmutable;
use Database\Factories\DogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property string $name
 * @property string|null $breed
 * @property int|null $age_months
 * @property Gender|null $gender
 * @property string|null $description
 * @property AdoptionStatus $adoption_status
 * @property bool $is_urgent
 * @property mixed|null $rescued_at
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static \Database\Factories\DogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereAdoptionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereAgeMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereBreed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereIsUrgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereRescuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog withoutTrashed()
 *
 * @property int $shelter_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dog whereShelterId($value)
 *
 * @property-read Shelter|null $shelter
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'name',
    'breed',
    'age_months',
    'gender',
    'description',
    'adoption_status',
    'is_urgent',
    'rescued_at',
    'shelter_id',
])]
class Dog extends Model implements HasMedia
{
    /** @use HasFactory<DogFactory> */
    use HasFactory;

    use InteractsWithMedia;
    use SoftDeletes;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')->useDisk('s3');
    }

    protected function casts(): array
    {
        return [
            'is_urgent' => 'boolean',
            'rescued_at' => 'datetime',
            'gender' => Gender::class,
            'adoption_status' => AdoptionStatus::class,
        ];
    }

    /** @return BelongsTo<Shelter, $this> */
    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    /** @return HasMany<Campaign, $this> */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
