<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function show()
    {
        $a = AboutContent::first() ?? AboutContent::firstOrCreate(['id' => 1]);
        return response()->json($this->format($a));
    }

    public function adminShow()
    {
        $a = AboutContent::current();
        return response()->json($this->format($a));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'story' => 'nullable|array',
            'vision' => 'nullable|string',
            'missions' => 'nullable|array',
            'stats' => 'nullable|array',
            'values' => 'nullable|array',
        ]);

        $about = AboutContent::current();
        $about->update([
            'story' => $request->input('story', $about->story),
            'vision' => $request->input('vision', $about->vision),
            'missions' => $request->input('missions', $about->missions),
            'stats' => $request->input('stats', $about->stats),
            'values' => $request->input('values', $about->values),
        ]);

        return response()->json($this->format($about));
    }

    private function format(AboutContent $a): array
    {
        // Normalize stats "value/label"
        $stats = is_array($a->stats) ? $a->stats : [];

        return [
            'story' => $a->story ?? [],
            'vision' => $a->vision ?? '',
            'missions' => $a->missions ?? [],
            'stats' => $stats,
            'values' => $a->values ?? [],
        ];
    }
}