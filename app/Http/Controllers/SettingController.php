<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    /**
     * Show the application settings form.
     */
    public function edit(): View
    {
        $settings = $this->settingService->all();

        return view('pages.settings.edit', compact('settings'));
    }

    /**
     * Update the application settings.
     */
    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle remove checkboxes
        if ($request->boolean('remove_logo')) {
            $this->settingService->removeFile('app_logo');
            unset($data['app_logo']);
        }

        if ($request->boolean('remove_favicon')) {
            $this->settingService->removeFile('app_favicon');
            unset($data['app_favicon']);
        }

        $this->settingService->update($data);

        return redirect()
            ->route('settings.edit')
            ->with('success', 'Application settings have been updated successfully.');
    }
}
