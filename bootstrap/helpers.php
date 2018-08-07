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
