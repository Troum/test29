<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class CarCreateDTO extends BaseDTO
{
    /**
     * @param int $carBrandId
     * @param int $carModelId
     * @param string|null $year
     * @param string|null $color
     * @param int|null $mileage
     */
    public function __construct(
        public readonly int $carBrandId,
        public readonly int $carModelId,
        public readonly ?string $year = null,
        public readonly ?string $color = null,
        public readonly ?int $mileage = null,
    ) {
    }

    /**
     * @param FormRequest $request
     * @return static
     */
    public static function fromRequest(FormRequest $request): static
    {
        $validated = $request->validated();

        return new static(
            carBrandId: $validated['car_brand_id'],
            carModelId: $validated['car_model_id'],
            year: $validated['year'] ?? null,
            color: $validated['color'] ?? null,
            mileage: $validated['mileage'] ?? null,
        );
    }
}
