<?php

namespace App\Http\Controllers\Core\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    /**
     * SessionController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        Auth::user()->clearSessions();

        return response()->json(['message' => 'You have successfully logged out.']);
    }
}
