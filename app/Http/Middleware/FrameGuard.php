<?php

namespace App\Http\Middleware;

use Closure;

class FrameGuard
{
    public function handle($request, Closure $next)
    {
          $response = $next($request);
               
          $response->headers->set('X-Frame-Options', 'ALLOWALL');
          $response->headers->set('Content-Security-Policy', "frame-ancestors 'self' *");

          return $response;
    }
}
