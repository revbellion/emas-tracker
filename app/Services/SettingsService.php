<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    public function getAll(): array
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::where('key', $key)->first()?->value ?? $default;
    }

    public function set(string $key, mixed $value): Setting
    {
        return Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public function update(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }
    }
}
