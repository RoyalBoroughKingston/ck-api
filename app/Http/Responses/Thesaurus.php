<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class Thesaurus implements Responsable
{
    /**
     * @var array
     */
    protected $thesaurus;

    /**
     * Thesaurus constructor.
     *
     * @param array $thesaurus
     */
    public function __construct(array $thesaurus)
    {
        $this->thesaurus = $thesaurus;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return response()->json(['data' => $this->thesaurus]);
    }
}
