<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        return response()->json(Testimonial::orderBy('sort')->get());
    }

    public function store(Request $request)
    {
        $testimonial = Testimonial::create($this->validated($request));
        return response()->json($testimonial, 201);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($this->validated($request));
        return response()->json($testimonial);
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return response()->json(['message' => 'Testimoni dihapus.'], 200);
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