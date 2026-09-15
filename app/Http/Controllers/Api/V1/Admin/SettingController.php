<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function publicIndex(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');

        return response()->json(['data' => $settings]);
    }

    public function index(): JsonResponse
    {
        $settings = Setting::all();

        return response()->json(['data' => $settings]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable', 'string'],
        ]);

        foreach ($data['settings'] as $item) {
            Setting::updateOrCreate(['key' => $item['key']], ['value' => $item['value']]);
        }

        return response()->json(['message' => 'Pengaturan disimpan.']);
    }
}
