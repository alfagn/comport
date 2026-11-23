<?php

namespace App\Core\DTOs;

/**
 * Minimal base DTO.
 *
 * DTOs carry validated input between the HTTP layer and services as typed,
 * immutable-ish objects instead of loose arrays. Each subclass lists its
 * fillable fields and may add derived attributes in fromArray().
 */
abstract class BaseData
{
    /**
     * Field names the DTO accepts from raw input. Anything not listed here
     * is dropped, which keeps mass assignment explicit and safe.
     */
    protected static array $fields = [];

    /**
     * Build a DTO from a validated request array.
     */
    public static function fromArray(array $attributes): static
    {
        $dto = new static();

        foreach (static::$fields as $field) {
            // property_exists keeps explicit nulls instead of skipping them.
            if (array_key_exists($field, $attributes)) {
                $dto->{$field} = $attributes[$field];
            }
        }

        return $dto;
    }

    /**
     * Export only the fields the DTO declared, ready for persistence.
     * Null values are kept so "clear this field" survives an update.
     */
    public function toArray(): array
    {
        $out = [];

        foreach (static::$fields as $field) {
            if (property_exists($this, $field)) {
                $out[$field] = $this->{$field};
            }
        }

        return $out;
    }
}
