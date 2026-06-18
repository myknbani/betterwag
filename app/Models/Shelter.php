<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\ShelterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone_number
 * @property string $location
 * @property string|null $description
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static \Database\Factories\ShelterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shelter withoutTrashed()
 *
 * @property-read Collection<int, Dog> $dogs
 * @property-read int|null $dogs_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 *
 * @mixin \Eloquent
 */
#[Fillable(['name', 'email', 'location', 'description', 'phone_number'])]
class Shelter extends Model
{
    /** @use HasFactory<ShelterFactory> */
    use HasFactory, SoftDeletes;

    /** @return HasMany<Dog, $this> */
    public function dogs(): HasMany
    {
        return $this->hasMany(Dog::class);
    }

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** @return HasMany<Campaign, $this> */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
