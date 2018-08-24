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

if (!function_exists('random_uk_phone')) {
    /**
     * Generate a random UK phone number.
     *
     * @return string
     */
    function random_uk_phone(): string
    {
        return array_random([
            '0'.rand(1000000000,1999999999),
            '0'.rand(7000000000,7999999999),
        ]);
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
        foreach ($arrayA as $keyA => $valueA) {
            if (in_array($valueA, $arrayB)) {
                unset($arrayA[$keyA]);
            }
        }

        return $arrayA;
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

if (!function_exists('occurrence')) {
    /**
     * Convert a number from 1 to 5 into an ordinal string.
     *
     * @param int $occurrence
     * @return string
     * @throws \InvalidArgumentException
     */
    function occurrence(int $occurrence): string
    {
        switch ($occurrence) {
            case 1:
                return 'first';
            case 2:
                return 'second';
            case 3:
                return 'third';
            case 4:
                return 'fourth';
            case 5:
                return 'last';
        }

        throw new InvalidArgumentException("[$occurrence] must be between 1-5");
    }
}

if (!function_exists('weekday')) {
    /**
     * Convert a number from 1 to 7 into an weekday string.
     *
     * @param int $weekday
     * @return string
     */
    function weekday(int $weekday): string
    {
        switch ($weekday) {
            case 1:
                return 'monday';
            case 2:
                return 'tuesday';
            case 3:
                return 'wednesday';
            case 4:
                return 'thursday';
            case 5:
                return 'friday';
            case 6:
                return 'saturday';
            case 7:
                return 'sunday';
        }

        throw new InvalidArgumentException("[$weekday] must be between 1-7");
    }
}

if (!function_exists('month')) {
    /**
     * Convert a number from 1 to 12 into an month string.
     *
     * @param int $month
     * @return string
     */
    function month(int $month): string
    {
        switch ($month) {
            case 1:
                return 'january';
            case 2:
                return 'february';
            case 3:
                return 'march';
            case 4:
                return 'april';
            case 5:
                return 'may';
            case 6:
                return 'june';
            case 7:
                return 'july';
            case 8:
                return 'august';
            case 9:
                return 'september';
            case 10:
                return 'october';
            case 11:
                return 'november';
            case 12:
                return 'december';
        }

        throw new InvalidArgumentException("[$month] must be between 1-12");
    }
}
