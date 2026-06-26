<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = Setting::current();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $settings = Setting::current();
        $data = $request->validated();

        if ($request->hasFile('family_logo')) {
            $data['family_logo'] = $request->file('family_logo')->store('logos', 'public');
        } else {
            unset($data['family_logo']);
        }

        $data['enable_leaderboard'] = $request->boolean('enable_leaderboard');
        $data['enable_badges'] = $request->boolean('enable_badges');

        $settings->update($data);

        ActivityLogger::log('settings.updated', 'Updated application settings');

        return back()->with('success', 'Settings saved successfully.');
    }
}
