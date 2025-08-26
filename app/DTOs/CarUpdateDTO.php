<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class CarUpdateDTO extends BaseDTO
{
    /**
     * @param int|null $carBrandId
     * @param int|null $carModelId
     * @param string|null $year
     * @param string|null $color
     * @param int|null $mileage
     */
    public function __construct(
        public readonly ?int $carBrandId = null,
        public readonly ?int $carModelId = null,
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
            carBrandId: $validated['car_brand_id'] ?? null,
            carModelId: $validated['car_model_id'] ?? null,
            year: $validated['year'] ?? null,
            color: $validated['color'] ?? null,
            mileage: $validated['mileage'] ?? null,
        );
    }
}
