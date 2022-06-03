<?php
namespace App\Http\Middleware;

use Closure;

class ContentDispositionHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var \Symfony\Component\HttpFoundation\BinaryFileResponse $response */
        $response = $next($request);

        $response->headers->set('Access-Control-Expose-Headers', 'Content-Disposition');

        return $response;
    }
}