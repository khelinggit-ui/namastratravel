<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $path = $request->file('image')->store('images', 'public');

        return response()->json([
            'url' => url('storage/'.$path),
            'path' => $path,
        ], 201);
    }

    public function destroy(Request $request)
    {
        $request->validate(['path' => 'required|string']);

        if ($request->input('path') && Storage::disk('public')->exists($request->input('path'))) {
            Storage::disk('public')->delete($request->input('path'));
        }

        return response()->json(['message' => 'File dihapus.']);
    }
}