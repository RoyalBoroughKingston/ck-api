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
        'analysis' => [
            'analyzer' => [
                'default' => [
                    'type' => 'standard',
                    'stopwords' => '_english_',
                ],
            ]
        ],
    ];
}
