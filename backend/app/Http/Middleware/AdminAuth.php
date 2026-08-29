<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       //mengecek apakah request memiliki user yang sah dari Sanctum
       if(! $request->user()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access'
        ], 401);
       }

       return $next($request);
    }
}
