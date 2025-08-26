<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @class CarModel
 * @property int $id
 * @property string $name
 * @property-read CarBrand $carBrand
 * @method static create(...$args)
 * @method static firstOrCreate(...$args)
 */
class CarModel extends Model
{
    use HasFactory;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'car_brand_id',
        'name',
    ];

    /**
     * @return BelongsTo
     */
    public function carBrand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class, 'car_brand_id');
    }
}
