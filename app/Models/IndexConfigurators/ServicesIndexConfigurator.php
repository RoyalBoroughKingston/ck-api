<?php

namespace App\Models\IndexConfigurators;

use Illuminate\Support\Collection;
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
                        'synonyms' => $this->getThesaurus(),
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

    /**
     * @return array
     */
    protected function getThesaurus(): array
    {
        $content = Storage::cloud()->get('elasticsearch/thesaurus.csv');
        $thesaurus = csv_to_array($content);

        $thesaurus = collect($thesaurus)->map(function (array $synonyms) {
            return collect($synonyms);
        });

        $thesaurus = $thesaurus
            ->map(function (Collection $synonyms) {
                return $synonyms
                    ->reject(function (string $term) {
                        // Filter out any empty strings.
                        return $term === '';
                    })
                    ->map(function (string $term) {
                        // Convert each term to lower case.
                        return strtolower($term);
                    })
                    ->implode(', ');
            })
            ->toArray();

        return $thesaurus;
    }
}
