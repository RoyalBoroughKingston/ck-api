<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class StopWords implements Responsable
{
    /**
     * @var array
     */
    protected $stopWords;

    /**
     * StopWords constructor.
     *
     * @param array $stopWords
     */
    public function __construct(array $stopWords)
    {
        $this->stopWords = $stopWords;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return response()->json(['data' => $this->stopWords]);
    }
}
