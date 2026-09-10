@extends('admin.layouts.app')
@section('title', $testimonial->exists ? 'Edit Testimoni' : 'Tambah Testimoni')
@section('page', $testimonial->exists ? 'Edit Testimoni' : 'Tambah Testimoni')

@section('content')
  <div class="card">
    <form method="POST" action="{{ $testimonial->exists ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}">
      @csrf
      @if ($testimonial->exists) @method('PUT') @endif
      <div class="form-grid">
        <div class="field"><label>Nama *</label><input name="name" value="{{ old('name', $testimonial->name) }}" required></div>
        <div class="field"><label>Lokasi</label><input name="location" value="{{ $testimonial->location }}"></div>
        <div class="field full"><label>Testimoni *</label><textarea name="text" rows="4" required>{{ $testimonial->text }}</textarea></div>
        <div class="field"><label>Rating (1-5)</label><input type="number" name="rating" min="1" max="5" value="{{ $testimonial->rating ?? 5 }}"></div>
        <div class="field"><label>Urutan</label><input type="number" name="sort" value="{{ $testimonial->sort ?? 0 }}"></div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
@endsection