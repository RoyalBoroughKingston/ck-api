<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;

class PreventReferralsDuringMaintenance extends PreventRequestsDuringMaintenance
{
    /**
     * Reject calls to referrals api if the app is in maintenance mode
     * This allows the frontend to function in maintenance mode but not make changes to the db
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next)
    {
        if ($request->is('core/v1/referrals*')) {
            return parent::handle($request, $next);
        }

        return $next($request);
    }
}
