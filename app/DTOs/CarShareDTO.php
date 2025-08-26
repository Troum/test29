<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class CarShareDTO extends BaseDTO
{
    /**
     * @param string $userEmail
     */
    public function __construct(
        public readonly string $userEmail
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
            userEmail: $validated['user_email']
        );
    }
}
