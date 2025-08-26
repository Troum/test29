<?php

namespace App\Models;

use App\Casts\DateCast;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @class Car
 * @property int $id
 * @property string $year
 * @property-read CarBrand $carBrand
 * @property-read CarModel $carModel
 * @property-read Collection<User> $users
 * @method static create(...$args)
 * @method static where(...$args)
 */
class Car extends Model
{
    use HasFactory;

    /**
     * @var array<string>
     */
    public $fillable = [
        'car_brand_id',
        'car_model_id',
        'year',
        'color',
        'mileage',
    ];

    /**
     * @var array<string,mixed>
     */
    protected $casts = [
        'year' => DateCast::class,
    ];

    /**
     * @return BelongsTo
     */
    public function carBrand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class, 'car_brand_id');
    }

    /**
     * @return BelongsTo
     */
    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_car')
            ->using(UserCar::class)
            ->withTimestamps();
    }

}
