<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Tour;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    use StoresImages;

    public function index()
    {
        return view('admin.destinations.index', ['destinations' => Destination::orderBy('sort')->get()]);
    }

    public function create()
    {
        return view('admin.destinations.form', ['destination' => new Destination, 'tours' => Tour::all()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $request->input('slug') ?: Str::slug($data['name']);
        $data['image'] = $this->storeImage($request, 'image');
        $destination = Destination::create($data);
        $this->syncGallery($destination, $this->storeGallery($request));
        $destination->tours()->sync($request->input('tour_ids', []));

        return redirect()->route('admin.destinations.index')->with('success', 'Destinasi berhasil ditambahkan.');
    }

    public function edit(Destination $destination)
    {
        return view('admin.destinations.form', [
            'destination' => $destination,
            'tours' => Tour::all(),
            'gallery' => Media::collection($destination->gallery),
        ]);
    }

    public function update(Request $request, Destination $destination)
    {
        $data = $this->validated($request);
        $data['slug'] = $request->input('slug') ?: $destination->slug;
        $data['image'] = $this->storeImage($request, 'image', $destination->image);
        $destination->update($data);
        $this->syncGallery($destination, $this->storeGallery($request, Media::collection($destination->gallery)));
        $destination->tours()->sync($request->input('tour_ids', []));

        return redirect()->route('admin.destinations.index')->with('success', 'Destinasi diperbarui.');
    }

    public function destroy(Destination $destination)
    {
        $this->deleteImage($destination->image);
        $destination->gallery()->each(fn ($g) => $this->deleteImage($g->image));
        $destination->delete();
        return redirect()->route('admin.destinations.index')->with('success', 'Destinasi dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'type' => 'nullable|in:Domestik,Mancanegara',
            'description' => 'nullable|string',
            'sort' => 'nullable|integer',
        ]);
    }

    private function syncGallery(Destination $destination, array $urls): void
    {
        $destination->gallery()->delete();
        foreach (array_values($urls) as $i => $url) {
            $destination->gallery()->create(['image' => $url, 'sort' => $i]);
        }
    }
}