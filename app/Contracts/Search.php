<?php

namespace App\Contracts;

use App\Support\Coordinate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface Search
{
    /**
     * @param string $term
     * @return \App\Contracts\Search
     */
    public function applyQuery(string $term): Search;

    /**
     * @param string $category
     * @return \App\Contracts\Search
     */
    public function applyCategory(string $category): Search;

    /**
     * @param string $persona
     * @return \App\Contracts\Search
     */
    public function applyPersona(string $persona): Search;

    /**
     * @param bool $isFree
     * @return \App\Contracts\Search
     */
    public function applyIsFree(bool $isFree): Search;

    /**
     * @param string $order
     * @param \App\Support\Coordinate|null $location
     * @return \App\Contracts\Search
     */
    public function applyOrder(string $order, Coordinate $location = null): Search;

    /**
     * @param int|null $perPage
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function paginate(int $perPage = null): AnonymousResourceCollection;

    /**
     * @param int|null $perPage
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function get(int $perPage = null): AnonymousResourceCollection;
}
