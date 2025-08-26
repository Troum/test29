<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCar extends Pivot
{
    /**
     * @var string
     */
    protected $table = 'user_car';

    /**
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'car_id',
    ];

    /**
     * @var bool
     */
    public $timestamps = true;
}
