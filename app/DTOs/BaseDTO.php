<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;
use ReflectionClass;
use ReflectionProperty;

/**
 * Базовый класс для Data Transfer Objects
 * 
 * Предоставляет общую функциональность для:
 * - Создания DTO из HTTP запросов
 * - Преобразования в массив для передачи в сервисы
 * - Проверки наличия данных
 * - Работы с camelCase/snake_case именами свойств
 */
abstract class BaseDTO
{
    /**
     * @param FormRequest $request
     * @return static
     */
    abstract public static function fromRequest(FormRequest $request): static;

    /**
     * @return array
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $data = [];

        foreach ($properties as $property) {
            $value = $property->getValue($this);
            if ($value !== null) {
                $key = $this->camelToSnake($property->getName());
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @return bool
     */
    public function hasData(): bool
    {
        return !empty($this->toArray());
    }

    /**
     * @param string $input
     * @return string
     */
    protected function camelToSnake(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    /**
     * @param string $propertyName
     * @return mixed
     */
    protected function getPropertyValue(string $propertyName): mixed
    {
        $camelProperty = lcfirst(str_replace('_', '', ucwords($propertyName, '_')));

        if (property_exists($this, $camelProperty)) {
            return $this->$camelProperty;
        }

        if (property_exists($this, $propertyName)) {
            return $this->$propertyName;
        }

        return null;
    }
}
