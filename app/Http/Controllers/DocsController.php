<?php

namespace App\Http\Controllers;

use App\Docs\OpenApi;

class DocsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('docs.index');
    }

    /**
     * Parse the specified YAML file through Blade.
     *
     * @throws \Throwable
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function show()
    {
        return OpenApi::create();
    }
}
