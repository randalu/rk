<?php

if (!function_exists('userCan')) {
    function userCan(string $permission): bool
    {
        $user = auth()->user();
        if (!$user || !$user->role) return false;

        $permissions = $user->role->permissions ?? [];
        return in_array($permission, $permissions);
    }
}

if (!function_exists('userRole')) {
    function userRole(): string
    {
        return auth()->user()?->role?->name ?? '';
    }
}