<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomQrFeatureEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR Design feature is disabled');
        }
        
        return $next($request);
    }
}