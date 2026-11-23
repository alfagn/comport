<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Services\SettingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Dashboard editor for grouped site settings.
 */
class SettingsController extends Controller
{
    public function __construct(private SettingService $settings) {}

    /** Show every setting, grouped. */
    public function edit(): View
    {
        return view('admin.settings.edit', ['groups' => $this->settings->grouped()]);
    }

    /** Persist the submitted settings. */
    public function update(SettingsRequest $request): RedirectResponse
    {
        $this->settings->saveGroup($request->validated('settings'));

        return back()->with('success', 'Settings saved.');
    }
}
