<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
  public function handle(Request $request, Closure $next, ...$roles): Response
  {
    $flattenedArray = array_merge($roles);
    if (Auth::check() && in_array(Auth::user()->role, $flattenedArray)) {
        return $next($request);
    }

    // redirect home if no access
    return redirect()->route('home')
            ->with('success', 'Invalid access');
  }
}


