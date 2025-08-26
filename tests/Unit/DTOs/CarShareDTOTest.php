<?php

namespace Tests\Unit\DTOs;

use App\DTOs\CarShareDTO;
use App\Http\Requests\CarShareRequest;
use Mockery;
use PHPUnit\Framework\TestCase;

class CarShareDTOTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_sets_properties_correctly(): void
    {
        $dto = new CarShareDTO(
            userEmail: 'test@example.com'
        );

        $this->assertEquals('test@example.com', $dto->userEmail);
    }

    public function test_from_request_creates_dto_correctly(): void
    {
        $request = Mockery::mock(CarShareRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andReturn([
                'user_email' => 'friend@example.com'
            ]);

        $dto = CarShareDTO::fromRequest($request);

        $this->assertEquals('friend@example.com', $dto->userEmail);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $dto = new CarShareDTO(
            userEmail: 'test@example.com'
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'user_email' => 'test@example.com'
        ], $array);
    }
}
