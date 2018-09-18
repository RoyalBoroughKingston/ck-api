<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        return view('auth.index');
    }
}
