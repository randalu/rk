<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemSettingController extends Controller
{
    public function edit()
    {
        abort_unless(userCan('manage_users'), 403);

        $settings = SystemSetting::query()->firstOrCreate(
            ['id' => 1],
            $this->defaultValues()
        );

        return view('settings.system', compact('settings'));
    }

    public function update(Request $request)
    {
        abort_unless(userCan('manage_users'), 403);

        $validated = $request->validate([
            'system_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_tagline' => 'nullable|string|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:1000',
            'company_registration_no' => 'nullable|string|max:255',
            'invoice_footer_heading' => 'nullable|string|max:255',
            'invoice_footer_notes' => 'nullable|string|max:2000',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $settings = SystemSetting::query()->firstOrCreate(
            ['id' => 1],
            $this->defaultValues()
        );

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $this->storeLogo($request, $settings);
        }

        $settings->update($validated);

        return redirect()->route('system-settings.edit')
            ->with('success', 'System settings updated successfully.');
    }

    private function storeLogo(Request $request, SystemSetting $settings): string
    {
        $directory = public_path('uploads/settings');
        File::ensureDirectoryExists($directory);

        if ($settings->logo_path && str_starts_with($settings->logo_path, 'uploads/settings/')) {
            File::delete(public_path($settings->logo_path));
        }

        $extension = strtolower($request->file('logo')->getClientOriginalExtension() ?: 'png');
        $filename = 'system-logo.' . $extension;

        $request->file('logo')->move($directory, $filename);

        return 'uploads/settings/' . $filename;
    }

    private function defaultValues(): array
    {
        return [
            'system_name' => config('app.name'),
            'logo_path' => File::exists(public_path('RK_logo.PNG')) ? 'RK_logo.PNG' : null,
            'company_name' => config('app.name'),
            'company_tagline' => 'Medical Sales & Distribution',
            'invoice_footer_heading' => 'Thank you for your business',
            'invoice_footer_notes' => null,
        ];
    }
}
