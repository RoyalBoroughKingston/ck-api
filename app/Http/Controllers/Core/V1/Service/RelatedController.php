<?php

namespace App\Http\Controllers\Core\V1\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\Related\Request;
use App\Models\Service;

class RelatedController extends Controller
{
    /**
     * @param \App\Http\Requests\Service\Related\Request $request
     * @param \App\Models\Service $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Service $service)
    {
        // TODO
    }
}
