<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private array $keys = [
        'name', 'tagline', 'address', 'phone', 'whatsapp', 'email',
        'hours', 'instagram', 'facebook', 'tiktok', 'maps_embed_url',
    ];

    public function edit()
    {
        return view('admin.settings.edit', ['settings' => Setting::getMany($this->keys)]);
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
        Setting::setMany($data);

        return redirect()->route('admin.settings')->with('success', 'Pengaturan disimpan.');
    }
}