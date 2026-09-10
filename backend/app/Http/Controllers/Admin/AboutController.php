<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function edit()
    {
        $about = AboutContent::current();
        $about->story = is_array($about->story) ? $about->story : [];
        $about->missions = is_array($about->missions) ? $about->missions : [];

        return view('admin.about.edit', ['about' => $about]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'vision' => 'nullable|string',
            'story' => 'nullable|string|max:10000',
            'missions' => 'nullable|string',
            'stats' => 'nullable|string',
            'values' => 'nullable|string',
        ]);

        $about = AboutContent::current();
        $about->update([
            'vision' => trim($data['vision'] ?? ''),
            'story' => array_values(array_filter(array_map('trim', preg_split('/\r?\n\s*\r?\n/', (string) ($data['story'] ?? '')) ?: []))),
            'missions' => $this->lines($data['missions']),
            'stats' => $this->pipePairs($data['stats']),
            'values' => collect($this->lines($data['values']))
                ->map(fn ($line) => ['title' => (str_contains($line, '|') ? trim(explode('|', $line, 2)[0]) : ''), 'desc' => (str_contains($line, '|') ? trim(explode('|', $line, 2)[1]) : '')])
                ->filter(fn ($v) => $v['title'] !== '')
                ->values()
                ->all(),
        ]);

        return redirect()->route('admin.about')->with('success', 'Konten Tentang Kami disimpan.');
    }

    private function lines(?string $value): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", (string) $value))));
    }

    private function pipePairs(?string $value): array
    {
        $out = [];
        foreach ($this->lines($value) as $line) {
            $parts = explode('|', $line, 2);
            $out[] = ['value' => trim($parts[0] ?? ''), 'label' => trim($parts[1] ?? '')];
        }
        return array_values(array_filter($out, fn ($p) => $p['value'] !== ''));
    }
}