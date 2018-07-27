<?php

namespace App\Models\Mutators;

trait UpdateRequestMutators
{
    /**
     * @param string $data
     * @return array
     */
    public function getDataAttribute(string $data): array
    {
        return json_decode($data, true);
    }

    /**
     * @param array $data
     */
    public function setDataAttribute(array $data)
    {
        $this->attributes['data'] = json_encode($data);
    }
}
