<?php

namespace App\IndexConfigurators;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class ServicesIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [
        //
    ];
}
