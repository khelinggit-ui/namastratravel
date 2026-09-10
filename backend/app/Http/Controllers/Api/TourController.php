<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourController extends Controller
{
    private function format(Tour $tour): array
    {
        return [
            'id' => $tour->id,
            'slug' => $tour->slug,
            'title' => $tour->title,
            'category' => $tour->category,
            'duration' => $tour->duration,
            'price_start' => $tour->price_start,
            'location' => $tour->location,
            'tagline' => $tour->tagline,
            'description' => $tour->description,
            'image' => Media::url($tour->image),
            'gallery' => Media::collection($tour->gallery),
            'tag' => $tour->tag,
            'status' => $tour->status,
            'highlights' => $tour->highlights ?: [],
            'itinerary' => $tour->itinerary ?: [],
            'departure_schedules' => $tour->departure_schedules ?: [],
            'destinations' => $tour->destinations->pluck('slug'),
        ];
    }

    public function index(Request $request)
    {
        $query = Tour::with(['gallery', 'destinations'])
            ->where('status', 'published');

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('q')) {
            $q = strtolower($request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->whereRaw('LOWER(title) like ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(location) like ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(tagline) like ?', ["%{$q}%"]);
            });
        }

        return response()->json($query->orderBy('sort')->get()->map(fn ($t) => $this->format($t)));
    }

    public function show(string $slug)
    {
        $tour = Tour::with(['gallery', 'destinations'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (! $tour) {
            return response()->json(['message' => 'Tour tidak ditemukan.'], 404);
        }

        return response()->json($this->format($tour));
    }

    // ---- Admin ----

    public function adminIndex(Request $request)
    {
        $query = Tour::with('gallery');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('q')) {
            $q = strtolower($request->input('q'));
            $query->whereRaw('LOWER(title) like ?', ["%{$q}%"]);
        }

        return response()->json($query->orderBy('sort')->paginate($request->input('per_page', 20)));
    }

    public function adminShow(Tour $tour)
    {
        $tour->load('gallery');
        return response()->json($tour);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title'] ?? 'tour', $request->input('slug'));
        $tour = Tour::create($data);
        $this->syncGallery($tour, $request->input('gallery', []));

        return response()->json($this->format($tour), 201);
    }

    public function update(Request $request, Tour $tour)
    {
        $data = $this->validated($request);
        if ($request->filled('slug')) {
            $data['slug'] = $this->uniqueSlug($request->input('slug'), $tour->slug, $tour->id);
        }
        $tour->update($data);
        $this->syncGallery($tour, $request->input('gallery', []));

        return response()->json($this->format($tour));
    }

    public function destroy(Tour $tour)
    {
        $tour->delete();
        return response()->json(['message' => 'Tour dihapus.'], 200);
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
            'image' => 'nullable|string|max:2048',
            'tag' => 'nullable|string|max:100',
            'status' => 'nullable|in:published,draft',
            'sort' => 'nullable|integer',
            'highlights' => 'nullable|array',
            'itinerary' => 'nullable|string|max:50000',
            'departure_schedules' => 'nullable|array',
            'departure_schedules.*.start_date' => 'required|date',
            'departure_schedules.*.end_date' => 'required|date|after_or_equal:departure_schedules.*.start_date',
            'departure_schedules.*.status' => 'required|in:available,full',
            'departure_schedules.*.price' => 'nullable|integer|min:0',
        ]);
    }

    private function syncGallery(Tour $tour, array $gallery): void
    {
        $tour->gallery()->delete();
        foreach (array_values($gallery) as $i => $url) {
            if (is_string($url) && $url !== '') {
                $tour->gallery()->create(['image' => $url, 'sort' => $i]);
            }
        }
    }

    private function uniqueSlug(string $base, ?string $existing = null, ?int $ignore = null): string
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
