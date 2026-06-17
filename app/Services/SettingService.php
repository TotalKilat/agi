<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    private const SETTING_KEYS = [
        'app_name',
        'app_description',
        'app_logo',
        'app_favicon',
    ];

    /**
     * Get all app settings as a flat key-value array.
     */
    public function all(): array
    {
        $settings = Setting::allAsArray();

        $defaults = [
            'app_name' => config('app.name', 'Agentix'),
            'app_description' => 'Agentic Intelligence Platform',
            'app_logo' => null,
            'app_favicon' => null,
        ];

        return array_merge($defaults, $settings);
    }

    /**
     * Update all application settings in a transaction.
     */
    public function update(array $data): void
    {
        DB::transaction(function () use ($data) {
            foreach (self::SETTING_KEYS as $key) {
                if (array_key_exists($key, $data)) {
                    if (in_array($key, ['app_logo', 'app_favicon'], true) && $data[$key] instanceof UploadedFile) {
                        $this->uploadFile($key, $data[$key]);
                    } elseif (! $data[$key] instanceof UploadedFile) {
                        Setting::set($key, $data[$key] ?: null);
                    }
                }
            }
        });
    }

    /**
     * Handle file upload and remove old file.
     */
    private function uploadFile(string $key, UploadedFile $file): void
    {
        $oldPath = Setting::get($key);

        $path = $file->store('settings', 'public');

        Setting::set($key, $path);

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    /**
     * Get the full public URL for a setting value, with fallback.
     */
    public function url(string $key, string $fallbackAsset = ''): string
    {
        $path = Setting::get($key);

        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return $fallbackAsset ? asset($fallbackAsset) : '';
    }

    /**
     * Remove a file-based setting (logo, favicon) from storage.
     */
    public function removeFile(string $key): void
    {
        $path = Setting::get($key);

        Setting::set($key, null);

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
