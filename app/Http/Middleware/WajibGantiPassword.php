<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WajibGantiPassword
{
    public function handle(Request $request, Closure $next)
    {
        $halaman = $request->route()->getName();
        $bolehDibuka = ['password.edit', 'password.update', 'logout'];
        $wajibGanti = $request->user()->internalProfile?->must_change_password;

        if ($wajibGanti && ! in_array($halaman, $bolehDibuka)) {
            return redirect()->route('password.edit');
        }

        return $next($request);
    }
}
