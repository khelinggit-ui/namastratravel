<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use StoresImages;

    public function index(Request $request)
    {
        $query = Post::query();
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->input('q').'%');
        }
        return view('admin.posts.index', ['posts' => $query->orderByDesc('published_at')->paginate(15)->withQueryString()]);
    }

    public function create()
    {
        return view('admin.posts.form', ['post' => new Post]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title'], null);
        $data['cover'] = $this->storeImage($request, 'cover');
        $data['body'] = json_encode($request->input('body_paragraphs', []), JSON_UNESCAPED_UNICODE);
        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Post $post)
    {
        $post->body_paragraphs = json_decode($post->body ?? '[]', true);
        return view('admin.posts.form', ['post' => $post]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title'], $post->slug, $post->id);
        $data['cover'] = $this->storeImage($request, 'cover', $post->cover);
        $data['body'] = json_encode($request->input('body_paragraphs', []), JSON_UNESCAPED_UNICODE);
        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Artikel diperbarui.');
    }

    public function destroy(Post $post)
    {
        $this->deleteImage($post->cover);
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Artikel dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:120',
            'published_at' => 'nullable|date',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:published,draft',
        ]);
    }

    private function uniqueSlug(string $base, ?string $existing, ?int $ignore = null): string
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