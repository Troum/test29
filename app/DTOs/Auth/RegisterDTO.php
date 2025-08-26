<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class RegisterDTO extends BaseDTO
{
    /**
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public readonly string $name,
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
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: Hash::make($request->validated('password')),
        );
    }
}
