<?php

namespace App\Models\IndexConfigurators;

use Illuminate\Support\Facades\Storage;
use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class ServicesIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getSettings(): array
    {
        return [
            'analysis' => [
                'analyzer' => [
                    'default' => [
                        'type' => 'standard',
                        'stopwords' => $this->getStopWords(),
                    ],
                ]
            ],
        ];
    }

    /**
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStopWords(): array
    {
        $json = Storage::disk('local')->get('elasticsearch/stopwords.json');
        $stopWords = json_decode($json, true);

        return $stopWords;
    }
}
