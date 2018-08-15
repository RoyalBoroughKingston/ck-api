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

if (!function_exists('table')) {
    /**
     * Returns the table name of a model.
     *
     * @param string $model
     * @param string|null $column
     * @return string|null
     */
    function table(string $model, string $column = null): ?string
    {
        if (!is_subclass_of($model, \Illuminate\Database\Eloquent\Model::class)) {
            throw new InvalidArgumentException("[$model] must be an instance of ".\Illuminate\Database\Eloquent\Model::class);
        }

        $table = (new $model())->getTable();

        return $column ? "$table.$column" : $table;
    }
}

if (!function_exists('single_space')) {
    /**
     * Removes duplicate spaces from a string.
     *
     * @param string $string
     * @return string
     */
    function single_space(string $string): string
    {
        $string = preg_replace('!\s+!', ' ', $string);
        $string = trim($string);

        return $string;
    }
}

if (!function_exists('strip_spaces')) {
    /**
     * Removes spaces from a string.
     *
     * @param string $string
     * @return string
     */
    function strip_spaces(string $string): string
    {
        return str_replace(' ', '', $string);
    }
}
