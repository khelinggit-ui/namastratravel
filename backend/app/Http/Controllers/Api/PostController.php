<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    private function format(Post $post): array
    {
        return [
            'id' => $post->id,
            'slug' => $post->slug,
            'title' => $post->title,
            'category' => $post->category,
            'author' => $post->author,
            'date' => optional($post->published_at)->toDateString(),
            'cover' => Media::url($post->cover),
            'excerpt' => $post->excerpt,
            'body' => json_decode($post->body ?? '[]', true),
        ];
    }

    public function index(Request $request)
    {
        $query = Post::where('status', 'published');

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }
        if ($request->filled('q')) {
            $q = strtolower($request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->whereRaw('LOWER(title) like ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(excerpt) like ?', ["%{$q}%"]);
            });
        }

        return response()->json($query->orderByDesc('published_at')->get()->map(fn ($p) => $this->format($p)));
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)->where('status', 'published')->first();
        if (! $post) {
            return response()->json(['message' => 'Artikel tidak ditemukan.'], 404);
        }
        return response()->json($this->format($post));
    }

    // ---- Admin ----

    public function adminIndex(Request $request)
    {
        $query = Post::query();
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('q')) {
            $query->whereRaw('LOWER(title) like ?', ['%'.strtolower($request->input('q')).'%']);
        }
        return response()->json($query->orderByDesc('published_at')->paginate($request->input('per_page', 20)));
    }

    public function adminShow(Post $post)
    {
        return response()->json($post);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['body'] = json_encode($request->input('body', []), JSON_UNESCAPED_UNICODE);
        $data['slug'] = $this->uniqueSlug($data['title'] ?? 'artikel', $request->input('slug'));
        $post = Post::create($data);
        return response()->json($this->format($post), 201);
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validated($request);
        $data['body'] = json_encode($request->input('body', []), JSON_UNESCAPED_UNICODE);
        if ($request->filled('slug')) {
            $data['slug'] = $this->uniqueSlug($request->input('slug'), $post->slug, $post->id);
        }
        $post->update($data);
        return response()->json($this->format($post));
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Artikel dihapus.'], 200);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:120',
            'published_at' => 'nullable|date',
            'cover' => 'nullable|string|max:2048',
            'excerpt' => 'nullable|string',
            'status' => 'nullable|in:published,draft',
        ]);
    }

    private function uniqueSlug(string $base, ?string $existing = null, ?int $ignore = null): string
    {
        if ($existing) {
            return $existing;
        }
        $slug = Str::slug($base);
        $i = 1;
        $candidate = $slug;
        while (Post::where('slug', $candidate)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))->exists()) {
            $candidate = $slug.'-'.$i++;
        }
        return $candidate;
    }
}