<?php

namespace App\Http\Controllers\Passport;

use App\Http\Controllers\Controller;
use Laravel\Passport\Passport;

class ClientController extends Controller
{
    /**
     * ClientController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Passport::client()
            ->whereNull('user_id')
            ->orderBy('name', 'asc')
            ->get();
    }
}
