<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoginDTO extends BaseDTO
{
    /**
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
    }

    /**
     * @param FormRequest $request
     * @return static
     */
    public static function fromRequest(FormRequest $request): static
    {
        return new static(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );
    }
}
