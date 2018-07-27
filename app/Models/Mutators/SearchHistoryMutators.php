<?php

namespace App\Models\Mutators;

trait SearchHistoryMutators
{
    /**
     * @param string $query
     * @return array
     */
    public function getQueryAttribute(string $query): array
    {
        return json_decode($query, true);
    }

    /**
     * @param array $query
     */
    public function setQueryAttribute(array $query)
    {
        $this->attributes['query'] = json_encode($query);
    }
}
