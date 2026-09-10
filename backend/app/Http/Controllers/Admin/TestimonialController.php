<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        return view('admin.testimonials.index', ['testimonials' => Testimonial::orderBy('sort')->get()]);
    }

    public function create()
    {
        return view('admin.testimonials.form', ['testimonial' => new Testimonial]);
    }

    public function store(Request $request)
    {
        Testimonial::create($this->validated($request));
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni ditambahkan.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', ['testimonial' => $testimonial]);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($this->validated($request));
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni diperbarui.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'text' => 'required|string',
            'rating' => 'nullable|integer|between:1,5',
            'sort' => 'nullable|integer',
        ]);
    }
}