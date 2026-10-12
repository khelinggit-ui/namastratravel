<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use Illuminate\Http\Request;

class HeroBannerController extends Controller
{
    use StoresImages;

    public function index()
    {
        return view('admin.master.banners.index', [
            'banners' => HeroBanner::orderBy('sort')->orderBy('id')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.master.banners.form', ['banner' => new HeroBanner]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(true));
        $data['image'] = $this->storeImage($request, 'image');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort'] = (int) ($data['sort'] ?? 0);

        HeroBanner::create($data);

        return redirect()->route('admin.master.banners.index')->with('success', 'Banner berhasil ditambahkan.');
    }

    public function edit(HeroBanner $banner)
    {
        return view('admin.master.banners.form', compact('banner'));
    }

    public function update(Request $request, HeroBanner $banner)
    {
        $data = $request->validate($this->rules(false));
        $oldImage = $banner->image;
        $data['image'] = $this->storeImage($request, 'image', $oldImage);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort'] = (int) ($data['sort'] ?? 0);
        $banner->update($data);

        if ($banner->image !== $oldImage) {
            $this->deleteImage($oldImage);
        }

        return redirect()->route('admin.master.banners.index')->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroy(HeroBanner $banner)
    {
        $this->deleteImage($banner->image);
        $banner->delete();

        return redirect()->route('admin.master.banners.index')->with('success', 'Banner berhasil dihapus.');
    }

    private function rules(bool $imageRequired): array
    {
        return [
            'title' => 'required|string|max:255',
            'image' => ($imageRequired ? 'required|' : 'nullable|').'image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort' => 'nullable|integer|min:0',
        ];
    }
}
