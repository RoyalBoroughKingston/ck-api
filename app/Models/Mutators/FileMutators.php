<?php

namespace App\Models\Mutators;

trait FileMutators
{
    /**
     * @param string|null $meta
     * @return array|null
     */
    public function getMetaAttribute(?string $meta): ?array
    {
        return ($meta === null) ? null : json_decode($meta, true);
    }

    /**
     * @param array|null $meta
     */
    public function setMetaAttribute(?array $meta)
    {
        $this->attributes['meta'] = ($meta === null) ? null : json_encode($meta);
    }
}
