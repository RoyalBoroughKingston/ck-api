<?php

namespace App\Models\Mutators;

trait ServiceMutators
{
    /**
     * @param string $accreditationLogos
     * @return array
     */
    public function getAccreditationLogosAttribute(string $accreditationLogos): array
    {
        return json_decode($accreditationLogos, true);
    }

    /**
     * @param array $accreditationLogos
     */
    public function setAccreditationLogosAttribute(array $accreditationLogos)
    {
        $this->attributes['accreditation_logos'] = json_encode($accreditationLogos);
    }
}
