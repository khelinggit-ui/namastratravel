<?php

namespace App\Support;

class Media
{
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }
        return url('storage/'.$path);
    }

    public static function collection(iterable $items): array
    {
        $out = [];
        foreach ($items as $item) {
            $out[] = self::url($item->image);
        }
        return $out;
    }
}