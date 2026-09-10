<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait StoresImages
{
    private function storeImage(Request $request, string $field, ?string $current = null): ?string
    {
        if ($request->hasFile($field)) {
            $request->validate([$field => 'image|mimes:jpeg,png,jpg,webp,gif|max:5120']);
            return $request->file($field)->store('images', 'public');
        }
        if ($request->boolean('remove_'.$field) && $current) {
            return null;
        }
        return $current;
    }

    private function storeGallery(Request $request, array $existing = []): array
    {
        $urls = $existing;
        $kept = $request->input('keep_gallery', []);

        // remove ones not in keep list
        $urls = array_values(array_filter($urls, fn ($u) => in_array($u, $kept, true)));

        if ($request->hasFile('gallery')) {
            $request->validate(['gallery.*' => 'image|mimes:jpg,jpeg,png,webp,gif|max:5120']);
            foreach ($request->file('gallery') as $file) {
                $urls[] = $file->store('images', 'public');
            }
        }
        return $urls;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && is_string($path) && ! preg_match('#^https?://#', $path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}