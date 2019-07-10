<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests as BaseThrottleRequests;

class ThrottleRequests extends BaseThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param int|string $maxAttempts
     * @param float|int $decayMinutes
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        // If not testing environment, then delegate to original logic in parent class.
        if (app()->environment() !== 'testing') {
            return parent::handle($request, $next, $maxAttempts, $decayMinutes);
        }

        // Don't rate limit when not in a testing environment.
        $key = $this->resolveRequestSignature($request);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }
}
