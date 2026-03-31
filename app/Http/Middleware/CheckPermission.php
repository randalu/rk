<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        $user = auth()->user();

        if (!$user || !$user->role) {
            abort(403, 'No role assigned.');
        }

        $permissions = $user->role->permissions ?? [];

        if (!in_array($permission, $permissions)) {
            abort(403, 'You do not have permission to do this.');
        }

        return $next($request);
    }
}