<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetorMiddlaware
{
    public function handle(Request $request, Closure $next)
    {
        if(!session()->get('setor')) {
            return redirect(route('setor.setor'));
        }
        return $next($request);
       }
}
