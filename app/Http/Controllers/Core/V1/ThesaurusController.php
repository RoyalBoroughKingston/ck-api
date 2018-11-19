<?php

namespace App\Http\Controllers\Core\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Thesaurus\IndexRequest;

class ThesaurusController extends Controller
{
    /**
     * ThesaurusController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:60,1');
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        //
    }
}
