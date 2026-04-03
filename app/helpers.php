<?php

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;

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

if (!function_exists('systemSettings')) {
    function systemSettings(): ?SystemSetting
    {
        static $settingsLoaded = false;
        static $settings = null;

        if ($settingsLoaded) {
            return $settings;
        }

        $settingsLoaded = true;

        try {
            if (Schema::hasTable('system_settings')) {
                $settings = SystemSetting::query()->first();
            }
        } catch (Throwable $e) {
            $settings = null;
        }

        return $settings;
    }
}

if (!function_exists('systemSetting')) {
    function systemSetting(string $key, $default = null)
    {
        $settings = systemSettings();

        if (!$settings) {
            return $default;
        }

        $value = data_get($settings, $key);

        return filled($value) ? $value : $default;
    }
}

if (!function_exists('systemLogoUrl')) {
    function systemLogoUrl(?string $default = 'RK_logo.PNG'): ?string
    {
        $path = systemSetting('logo_path', $default);

        return $path ? asset($path) : null;
    }
}

if (!function_exists('systemLogoPath')) {
    function systemLogoPath(?string $default = 'RK_logo.PNG'): ?string
    {
        $path = systemSetting('logo_path', $default);

        return $path ? public_path(str_replace('/', DIRECTORY_SEPARATOR, ltrim($path, '/'))) : null;
    }
}

if (!function_exists('systemTemplateRender')) {
    function systemTemplateRender(?string $template, array $variables = []): string
    {
        $template ??= '';

        $replacements = [];

        foreach ($variables as $key => $value) {
            $replacements['{' . $key . '}'] = (string) $value;
        }

        return strtr($template, $replacements);
    }
}
