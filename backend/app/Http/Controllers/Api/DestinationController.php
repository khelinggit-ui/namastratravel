<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    private function format(Destination $destination, bool $withTours = false): array
    {
        $data = [
            'id' => $destination->id,
            'slug' => $destination->slug,
            'name' => $destination->name,
            'country' => $destination->country,
            'type' => $destination->type,
            'description' => $destination->description,
            'image' => Media::url($destination->image),
            'gallery' => Media::collection($destination->gallery),
        ];

        if ($withTours) {
            $data['tours'] = $destination->tours
                ->where('status', 'published')
                ->map(fn ($t) => [
                    'id' => $t->id,
                    'slug' => $t->slug,
                    'title' => $t->title,
                    'category' => $t->category,
                    'duration' => $t->duration,
                    'price_start' => $t->price_start,
                    'location' => $t->location,
                    'tagline' => $t->tagline,
                    'image' => Media::url($t->image),
                    'tag' => $t->tag,
                ])->values();
        }

        return $data;
    }

    public function index()
    {
        $destinations = Destination::with('gallery')->orderBy('sort')->get();
        return response()->json($destinations->map(fn ($d) => $this->format($d)));
    }

    public function show(string $slug)
    {
        $destination = Destination::with(['gallery', 'tours.gallery'])
            ->where('slug', $slug)
            ->first();

        if (! $destination) {
            return response()->json(['message' => 'Destinasi tidak ditemukan.'], 404);
        }

        return response()->json($this->format($destination, true));
    }

    // ---- Admin ----

    public function adminIndex()
    {
        return response()->json(Destination::with('gallery')->orderBy('sort')->get());
    }

    public function adminShow(Destination $destination)
    {
        $destination->load(['gallery', 'tours']);
        return response()->json($destination);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $request->input('slug') ?: Str::slug($data['name']);
        $destination = Destination::create($data);
        $this->sync($destination, $request->input('gallery', []), $request->input('tour_ids', []));

        return response()->json($this->format($destination, true), 201);
    }

    public function update(Request $request, Destination $destination)
    {
        $data = $this->validated($request);
        if ($request->filled('slug')) {
            $data['slug'] = $request->input('slug');
        }
        $destination->update($data);
        $this->sync($destination, $request->input('gallery', []), $request->input('tour_ids', []));

        return response()->json($this->format($destination, true));
    }

    public function destroy(Destination $destination)
    {
        $destination->delete();
        return response()->json(['message' => 'Destinasi dihapus.'], 200);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'type' => 'nullable|in:Domestik,Mancanegara',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:2048',
            'sort' => 'nullable|integer',
        ]);
    }

    private function sync(Destination $destination, array $gallery, array $tourIds): void
    {
        $destination->gallery()->delete();
        foreach (array_values($gallery) as $i => $url) {
            if (is_string($url) && $url !== '') {
                $destination->gallery()->create(['image' => $url, 'sort' => $i]);
            }
        }
        $destination->tours()->sync(collect($tourIds)->filter());
    }
}