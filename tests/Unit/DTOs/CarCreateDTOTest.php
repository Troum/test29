<?php

namespace Tests\Unit\DTOs;

use App\DTOs\CarCreateDTO;
use App\Http\Requests\CarCreateRequest;
use Mockery;
use PHPUnit\Framework\TestCase;

class CarCreateDTOTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_sets_properties_correctly(): void
    {
        $dto = new CarCreateDTO(
            carBrandId: 2,
            carModelId: 3,
            year: '2020',
            color: 'Красный',
            mileage: 15000
        );

        $this->assertEquals(2, $dto->carBrandId);
        $this->assertEquals(3, $dto->carModelId);
        $this->assertEquals('2020', $dto->year);
        $this->assertEquals('Красный', $dto->color);
        $this->assertEquals(15000, $dto->mileage);
    }

    public function test_constructor_with_nullable_fields(): void
    {
        $dto = new CarCreateDTO(
            carBrandId: 2,
            carModelId: 3
        );

        $this->assertEquals(2, $dto->carBrandId);
        $this->assertEquals(3, $dto->carModelId);
        $this->assertNull($dto->year);
        $this->assertNull($dto->color);
        $this->assertNull($dto->mileage);
    }

    public function test_from_request_creates_dto_correctly(): void
    {
        $request = Mockery::mock(CarCreateRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andReturn([
                'car_brand_id' => 2,
                'car_model_id' => 3,
                'year' => '2020',
                'color' => 'Синий',
                'mileage' => 25000
            ]);

        $dto = CarCreateDTO::fromRequest($request);

        $this->assertEquals(2, $dto->carBrandId);
        $this->assertEquals(3, $dto->carModelId);
        $this->assertEquals('2020', $dto->year);
        $this->assertEquals('Синий', $dto->color);
        $this->assertEquals(25000, $dto->mileage);
    }

    public function test_from_request_handles_missing_optional_fields(): void
    {
        $request = Mockery::mock(CarCreateRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andReturn([
                'car_brand_id' => 2,
                'car_model_id' => 3
            ]);

        $dto = CarCreateDTO::fromRequest($request);

        $this->assertEquals(2, $dto->carBrandId);
        $this->assertEquals(3, $dto->carModelId);
        $this->assertNull($dto->year);
        $this->assertNull($dto->color);
        $this->assertNull($dto->mileage);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $dto = new CarCreateDTO(
            carBrandId: 2,
            carModelId: 3,
            year: '2020',
            color: 'Красный',
            mileage: 15000
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'car_brand_id' => 2,
            'car_model_id' => 3,
            'year' => '2020',
            'color' => 'Красный',
            'mileage' => 15000
        ], $array);
    }

    public function test_to_array_excludes_null_values(): void
    {
        $dto = new CarCreateDTO(
            carBrandId: 2,
            carModelId: 3
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'car_brand_id' => 2,
            'car_model_id' => 3
        ], $array);
    }
}