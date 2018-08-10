<?php

if (!function_exists('uuid')) {
    /**
     * Generate a UUID (version 4).
     *
     * @return string
     */
    function uuid(): string
    {
        return \Illuminate\Support\Str::uuid()->toString();
    }
}

if (!function_exists('array_filter_null')) {
    /**
     * Removed any array values with a null value.
     *
     * @param array $array
     * @return array
     */
    function array_filter_null(array $array): array
    {
        return array_filter($array, function ($value) {
            return $value !== null;
        });
    }
}

if (!function_exists('array_pluck_multi')) {
    /**
     * Plucks a key from a multidimensional array.
     *
     * @param array $array
     * @param string $value
     * @return array
     */
    function array_pluck_multi(array $array, string $value): array
    {
        return collect($array)->pluck($value)->toArray();
    }
}

if (!function_exists('array_diff_multi')) {
    /**
     * Diffs an array from a multidimensional array.
     *
     * @param array $arrayA
     * @param array $arrayB
     * @return array
     */
    function array_diff_multi(array $arrayA, array $arrayB): array
    {
        return array_udiff($arrayA, $arrayB, function ($arrayA, $arrayB): int {
            return count(array_diff_assoc($arrayA, $arrayB));
        });
    }
}
