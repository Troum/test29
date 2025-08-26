<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @class CarBrand
 * @property int $id
 * @property string $name
 * @property-read Collection<CarModel> $carModels
 * @property-read Collection<Car> $cars
 * @method static create(...$args)
 * @method static firstOrCreate(...$args)
 * @method static where(...$args)
 */
class CarBrand extends Model
{
    use HasFactory;

    /**
     * @var array<string>
     */
    protected $fillable = [
      'name'
    ];

    /**
     * @return HasMany
     */
    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class, 'car_brand_id');
    }

    /**
     * @return HasMany
     */
    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'car_brand_id');
    }
}
