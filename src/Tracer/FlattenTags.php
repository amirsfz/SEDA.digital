<?php

declare(strict_types=1);

namespace SEDAdigital\Tracing\Tracer;

trait FlattenTags
{
    public function getFlattenedTags(array $tags): array
    {
        $flat = [];
        foreach ($tags as $key => $value) {
            if (is_array($value)) {
                foreach ($this->flattenValue($value, $key) as $nestedKey => $nestedValue) {
                    $flat[$nestedKey] = $nestedValue;
                }
                continue;
            }
            $flat[$key] = $value;
        }

        return $flat;
    }

    private function flattenValue(array $value, string $parent): array
    {
        // @TODO check with iterators
        $local = [];
        foreach ($value as $key => $nestedValue) {
            if (is_numeric($key) && !is_array($nestedValue)) {
                $local[$parent] = implode(', ', $value);
                return $local;
            }
            if (is_array($nestedValue)) {
                foreach ($this->flattenValue($nestedValue, "{$parent}.{$key}") as $nestedKey => $nestedResult) {
                    $local[$nestedKey] = $nestedResult;
                }
                continue;
            }
            $local["{$parent}.{$key}"] = $nestedValue;
        }

        return $local;
    }
}
