<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) continue;

            // Handle image uploads
            if ($setting->type === 'image' && $request->hasFile("files.{$key}")) {
                // Delete old image if exists
                if ($setting->value) {
                    Storage::disk('public')->delete($setting->value);
                }
                $value = $request->file("files.{$key}")->store('settings', 'public');
            }

            // Handle boolean
            if ($setting->type === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            }

            $setting->update(['value' => $value]);
        }

        // Clear all setting caches
        Cache::forget('settings.all');
        Setting::all()->each(function ($s) {
            Cache::forget("setting.{$s->key}");
        });

        return Redirect::back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
