<?php

namespace App\Models\Mutators;

trait CollectionMutators
{
    /**
     * @param string $meta
     * @return array
     */
    public function getMetaAttribute(string $meta): array
    {
        return json_decode($meta, true);
    }

    /**
     * @param array $meta
     */
    public function setMetaAttribute(array $meta)
    {
        $this->attributes['meta'] = json_encode($meta);
    }
}
