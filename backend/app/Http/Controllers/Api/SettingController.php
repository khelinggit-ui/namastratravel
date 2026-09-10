<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private array $keys = [
        'name', 'tagline', 'address', 'phone', 'whatsapp', 'email',
        'hours', 'instagram', 'facebook', 'tiktok', 'maps_embed_url',
    ];

    public function show()
    {
        return response()->json(Setting::getMany($this->keys));
    }

    public function adminShow()
    {
        return response()->json(Setting::getMany($this->keys));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:40',
            'whatsapp' => 'nullable|string|max:40',
            'email' => 'nullable|email',
            'hours' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'maps_embed_url' => 'nullable|string',
        ]);

        Setting::setMany($request->only($this->keys));

        return response()->json(Setting::getMany($this->keys));
    }
}