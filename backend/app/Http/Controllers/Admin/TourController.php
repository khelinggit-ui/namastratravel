<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourController extends Controller
{
    use StoresImages;

    public function index(Request $request)
    {
        $query = Tour::query();
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->input('q').'%');
        }
        return view('admin.tours.index', ['tours' => $query->orderBy('sort')->paginate(15)->withQueryString()]);
    }

    public function create()
    {
        return view('admin.tours.form', ['tour' => new Tour, 'destinations' => \App\Models\Destination::all()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title'], null);
        $data['description'] = $this->cleanRichText($request->input('description'));
        $data['highlights'] = $this->parseHighlights($request);
        $data['itinerary'] = $this->parseItinerary($request->input('itinerary', []));
        $data['departure_schedules'] = $this->parseSchedules($request->input('departure_schedules'));
        $data['image'] = $this->storeImage($request, 'image');
        $tour = Tour::create($data);
        $this->syncGallery($tour, $this->storeGallery($request));
        $tour->destinations()->sync($request->input('destination_ids', []));

        return redirect()->route('admin.tours.index')->with('success', 'Tour berhasil ditambahkan.');
    }

    public function edit(Tour $tour)
    {
        return view('admin.tours.form', [
            'tour' => $tour,
            'itinerary' => $this->itineraryForEditor($tour->itinerary),
            'destinations' => \App\Models\Destination::all(),
            'gallery' => Media::collection($tour->gallery),
        ]);
    }

    public function update(Request $request, Tour $tour)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title'], $tour->slug, $tour->id);
        $data['description'] = $this->cleanRichText($request->input('description'));
        $data['highlights'] = $this->parseHighlights($request);
        $data['itinerary'] = $this->parseItinerary($request->input('itinerary', []));
        $data['departure_schedules'] = $this->parseSchedules($request->input('departure_schedules'));
        $data['image'] = $this->storeImage($request, 'image', $tour->image);
        $tour->update($data);
        $this->syncGallery($tour, $this->storeGallery($request, Media::collection($tour->gallery)));
        $tour->destinations()->sync($request->input('destination_ids', []));

        return redirect()->route('admin.tours.index')->with('success', 'Tour diperbarui.');
    }

    public function destroy(Tour $tour)
    {
        $this->deleteImage($tour->image);
        $tour->gallery()->each(fn ($g) => $this->deleteImage($g->image));
        $tour->delete();
        return redirect()->route('admin.tours.index')->with('success', 'Tour dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:domestik,mancanegara',
            'duration' => 'nullable|string|max:100',
            'price_start' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tag' => 'nullable|string|max:100',
            'status' => 'required|in:published,draft',
            'sort' => 'nullable|integer',
            'itinerary' => 'nullable|array',
            'itinerary.*.day' => 'required|string|max:20',
            'itinerary.*.route' => 'required|string|max:255',
            'itinerary.*.details' => 'nullable|string|max:10000',
            'departure_schedules' => 'nullable|string',
        ]);
    }

    private function parseHighlights(Request $request): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", (string) $request->input('highlights')))));
    }

    private function parseItinerary(array $items): array
    {
        return array_values(array_filter(array_map(function ($item) {
            if (! is_array($item) || empty(trim((string) ($item['route'] ?? '')))) return null;
            return [
                'day' => trim((string) ($item['day'] ?? '')),
                'route' => trim((string) $item['route']),
                'details' => $this->cleanRichText($item['details'] ?? '') ?? '',
            ];
        }, $items)));
    }

    private function itineraryForEditor($value): array
    {
        if (! $value) return [];
        $items = is_array($value) ? $value : json_decode((string) $value, true);
        if (! is_array($items)) return [];
        if (isset($items[0]) && is_array($items[0])) return $items;
        return [['day' => '1', 'route' => 'Rencana Perjalanan', 'details' => implode(' ', array_map('strval', $items))]];
    }

    private function cleanRichText(?string $value): ?string
    {
        if (! $value) return null;
        $html = strip_tags($value, '<p><br><strong><em><u><s><ol><ul><li><h1><h2><h3><blockquote><a>');
        $html = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/i', '', $html);
        return preg_replace('/\s+href\s*=\s*(["\'])(?!https?:\/\/|mailto:).*?\1/i', '', $html);
    }

    private function parseSchedules(?string $value): array
    {
        return array_values(array_filter(array_map(function ($line) {
            $parts = array_map('trim', explode('|', $line));
            if (count($parts) < 3 || ! $parts[0] || ! $parts[1]) return null;
            return [
                'start_date' => $parts[0],
                'end_date' => $parts[1],
                'status' => in_array($parts[2], ['available', 'full'], true) ? $parts[2] : 'available',
                'price' => isset($parts[3]) && is_numeric($parts[3]) ? (int) $parts[3] : null,
            ];
        }, explode("\n", (string) $value))));
    }

    private function syncGallery(Tour $tour, array $urls): void
    {
        $tour->gallery()->delete();
        foreach (array_values($urls) as $i => $url) {
            $tour->gallery()->create(['image' => $url, 'sort' => $i]);
        }
    }

    private function uniqueSlug(string $base, ?string $existing, ?int $ignore = null): string
    {
        if ($existing) {
            return $existing;
        }
        $slug = Str::slug($base);
        $i = 1;
        $candidate = $slug;
        while (Tour::where('slug', $candidate)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))->exists()) {
            $candidate = $slug.'-'.$i++;
        }
        return $candidate;
    }
}
