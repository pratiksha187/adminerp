<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $roleId = (int) ($user->role ?? 0);

        // roles passed like: role:1,2,17
        $allowed = array_map('intval', $roles);

        if (!in_array($roleId, $allowed, true)) {
            abort(403, 'You are not allowed to access this page.');
            // OR: return redirect()->route('dashboard')->with('error','No permission');
        }

        return $next($request);
    }
}
