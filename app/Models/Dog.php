<?php

namespace App\Models;

use App\Enums\AdoptionStatus;
use App\Enums\Gender;
use Carbon\CarbonImmutable;
use Database\Factories\DogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string|null $breed
 * @property int|null $age_months
 * @property string|null $gender
 * @property string|null $description
 * @property string $adoption_status
 * @property bool $is_urgent
 * @property string|null $rescued_at
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
 * @mixin \Eloquent
 */
class Dog extends Model
{
    /** @use HasFactory<DogFactory> */
    use HasFactory;

    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_urgent' => 'boolean',
            'rescued_at' => 'dattime',
            'gender' => Gender::class,
            'adoption_status' => AdoptionStatus::class,
        ];
    }
}
