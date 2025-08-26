<?php

namespace Tests\Unit\DTOs;

use App\DTOs\CarUpdateDTO;
use App\Http\Requests\CarUpdateRequest;
use Mockery;
use PHPUnit\Framework\TestCase;

class CarUpdateDTOTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_sets_properties_correctly(): void
    {
        $dto = new CarUpdateDTO(
            carBrandId: 2,
            carModelId: 3,
            year: '2021',
            color: 'Зеленый',
            mileage: 30000
        );

        $this->assertEquals(2, $dto->carBrandId);
        $this->assertEquals(3, $dto->carModelId);
        $this->assertEquals('2021', $dto->year);
        $this->assertEquals('Зеленый', $dto->color);
        $this->assertEquals(30000, $dto->mileage);
    }

    public function test_constructor_with_all_null_values(): void
    {
        $dto = new CarUpdateDTO();

        $this->assertNull($dto->carBrandId);
        $this->assertNull($dto->carModelId);
        $this->assertNull($dto->year);
        $this->assertNull($dto->color);
        $this->assertNull($dto->mileage);
    }

    public function test_from_request_creates_dto_correctly(): void
    {
        $request = Mockery::mock(CarUpdateRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andReturn([
                'car_brand_id' => 2,
                'year' => '2021',
                'color' => 'Синий'
            ]);

        $dto = CarUpdateDTO::fromRequest($request);

        $this->assertEquals(2, $dto->carBrandId);
        $this->assertNull($dto->carModelId);
        $this->assertEquals('2021', $dto->year);
        $this->assertEquals('Синий', $dto->color);
        $this->assertNull($dto->mileage);
    }

    public function test_from_request_handles_empty_data(): void
    {
        $request = Mockery::mock(CarUpdateRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andReturn([]);

        $dto = CarUpdateDTO::fromRequest($request);

        $this->assertNull($dto->carBrandId);
        $this->assertNull($dto->carModelId);
        $this->assertNull($dto->year);
        $this->assertNull($dto->color);
        $this->assertNull($dto->mileage);
    }

    public function test_to_array_returns_only_non_null_values(): void
    {
        $dto = new CarUpdateDTO(
            carBrandId: 2,
            year: '2021',
            mileage: 40000
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'car_brand_id' => 2,
            'year' => '2021',
            'mileage' => 40000
        ], $array);
    }

    public function test_to_array_returns_empty_when_all_null(): void
    {
        $dto = new CarUpdateDTO();

        $array = $dto->toArray();

        $this->assertEquals([], $array);
    }

    public function test_has_data_returns_true_when_data_exists(): void
    {
        $dto = new CarUpdateDTO(carBrandId: 2);

        $this->assertTrue($dto->hasData());
    }

    public function test_has_data_returns_false_when_no_data(): void
    {
        $dto = new CarUpdateDTO();

        $this->assertFalse($dto->hasData());
    }

    public function test_explicit_null_values_are_included(): void
    {
        $request = Mockery::mock(CarUpdateRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andReturn([
                'color' => null,
                'mileage' => null
            ]);

        $dto = CarUpdateDTO::fromRequest($request);

        $this->assertNull($dto->color);
        $this->assertNull($dto->mileage);

        $array = $dto->toArray();
        $this->assertEquals([], $array);
    }
}
