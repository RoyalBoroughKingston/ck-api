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

if (!function_exists('is_uuid')) {
    /**
     * Check a string to see if it is a valid UUID
     *
     * @param string $uuid
     * @return bool
     **/
    function is_uuid(string $uuid): bool
    {
        return 1 === preg_match('/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/', $uuid);
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
        return \Illuminate\Support\Arr::random([
            '0' . rand(1000000000, 1999999999),
            '0' . rand(7000000000, 7999999999),
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

if (!function_exists('array_filter_missing')) {
    /**
     * Removed any array values with an empty value.
     *
     * @param array $array
     * @return array
     */
    function array_filter_missing(array $array): array
    {
        return array_filter($array, function ($value) {
            return !($value instanceof \App\Support\MissingValue);
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

        return array_values($arrayA);
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
            throw new InvalidArgumentException("[$model] must be an instance of " . \Illuminate\Database\Eloquent\Model::class);
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

if (!function_exists('register_enum_type')) {
    /**
     * Registers the enum type as a string.
     */
    function register_enum_type()
    {
        \Illuminate\Support\Facades\Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');
    }
}

if (!function_exists('per_page')) {
    /**
     * @param int|null $perPage
     * @return int
     */
    function per_page(int $perPage = null): int
    {
        $perPage = $perPage ?? config('ck.pagination_results');

        $perPage = min(config('ck.max_pagination_results'), $perPage);
        $perPage = max(1, $perPage);

        return $perPage;
    }
}

if (!function_exists('page')) {
    /**
     * @param int|null $page
     * @return int
     */
    function page(int $page = null): int
    {
        $page = $page ?? 1;

        $page = max(1, $page);

        return $page;
    }
}

if (!function_exists('backend_uri')) {
    /**
     * @param string $path
     * @return string
     */
    function backend_uri(string $path = ''): string
    {
        return config('ck.backend_uri') . $path;
    }
}

if (!function_exists('csv_to_array')) {
    /**
     * @param string $content
     * @return array
     */
    function csv_to_array(string $content): array
    {
        $rows = str_getcsv($content, "\n");

        foreach ($rows as &$row) {
            $row = str_getcsv($row);

            // Remove quotes from quotes cells.
            foreach ($row as &$cell) {
                $cell = trim($cell, '"');
            }
        }

        return $rows;
    }
}

if (!function_exists('array_to_csv')) {
    /**
     * @see https://coderwall.com/p/zvzwwa/array-to-comma-separated-string-in-php For source of function
     * @param array $data
     * @return string
     */
    function array_to_csv(array $data): string
    {
        // Generate CSV data from array.
        $fh = fopen('php://temp', 'rw');

        // Write out the data.
        foreach ($data as $row) {
            // Wrap cells with coma's in double quotes.
            foreach ($row as &$cell) {
                $cell = \Illuminate\Support\Str::contains($cell, ',') ? '"' . $cell . '"' : $cell;
            }

            fputcsv($fh, $row);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $csv;
    }
}

if (!function_exists('combine_query')) {
    /**
     * Outputs the query with bindings inserted.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return string
     */
    function combine_query(\Illuminate\Database\Eloquent\Builder $query): string
    {
        return vsprintf(
            str_replace('?', '%s', $query->toSql()),
            collect($query->getBindings())->map(function ($binding) {
                return is_numeric($binding) ? $binding : "'{$binding}'";
            })->toArray()
        );
    }
}

if (!function_exists('sanitize_markdown')) {
    /**
     * Sanitizes the markdown from unwanted markup.
     *
     * @param string $markdown
     * @return string
     */
    function sanitize_markdown(string $markdown): string
    {
        // Strip all HTML tags.
        $markdown = strip_tags($markdown);

        // Hard removal of XSS.
        $markdown = str_replace('javascript:', '', $markdown);

        return $markdown;
    }
}

if (!function_exists('trim_quotes')) {
    /**
     * Trim a string from all types of quotes.
     *
     * @param string $string
     * @return string
     */
    function trim_quotes(string $string): string
    {
        return trim($string, '‘’“”\'\'""');
    }
}
