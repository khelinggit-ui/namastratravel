<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getMany(array $keys): array
    {
        $rows = static::whereIn('key', $keys)->pluck('value', 'key')->toArray();
        $defaults = [
            'name' => 'Namastra Travel',
            'tagline' => 'Jelajah Dunia Bersama Kami',
            'address' => '',
            'phone' => '',
            'whatsapp' => '',
            'email' => '',
            'hours' => '',
            'instagram' => '',
            'facebook' => '',
            'tiktok' => '',
            'maps_embed_url' => '',
        ];
        return array_merge($defaults, $rows);
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }
    }
}