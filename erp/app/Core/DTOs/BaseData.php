<?php

declare(strict_types=1);

namespace App\Core\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class BaseData
 *
 * Immutable Data Transfer Object base adhering to Clean Architecture.
 * Decouples presentation payloads from domain logic.
 */
abstract readonly class BaseData implements Arrayable, JsonSerializable
{
    /**
     * Convert DTO to associative array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $result = [];

        foreach ($properties as $property) {
            $value = $property->getValue($this);

            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            } elseif ($value instanceof JsonSerializable) {
                $value = $value->jsonSerialize();
            }

            $result[$property->getName()] = $value;
        }

        return $result;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
