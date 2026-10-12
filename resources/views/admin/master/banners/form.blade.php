@extends('admin.layouts.app')
@section('title', $banner->exists ? 'Edit Banner Hero' : 'Tambah Banner Hero')
@section('page', 'Master / Banner Hero')

@section('actions')
  <a href="{{ route('admin.master.banners.index') }}" class="btn btn-outline">Kembali</a>
@endsection

@section('content')
  <div class="card master-form-card">
    <form method="POST" action="{{ $banner->exists ? route('admin.master.banners.update', $banner) : route('admin.master.banners.store') }}" enctype="multipart/form-data">
      @csrf
      @if ($banner->exists) @method('PUT') @endif
      <div class="form-grid">
        <div class="field full">
          <label for="banner-title">Judul banner *</label>
          <input id="banner-title" name="title" value="{{ old('title', $banner->title) }}" required maxlength="255">
        </div>
        <div class="field full">
          <label for="banner-image">Gambar {{ $banner->exists ? '(kosongkan jika tidak diganti)' : '*' }}</label>
          <input id="banner-image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif" {{ $banner->exists ? '' : 'required' }}>
          <p class="muted">JPG, PNG, WEBP, atau GIF. Maksimal 5 MB.</p>
          @if ($banner->image)
            <div class="editor-current-image"><img src="{{ \App\Support\Media::url($banner->image) }}" alt="{{ $banner->alt_text ?: $banner->title }}"></div>
          @endif
        </div>
        <div class="field full">
          <label for="banner-alt">Teks alternatif gambar</label>
          <input id="banner-alt" name="alt_text" value="{{ old('alt_text', $banner->alt_text) }}" maxlength="255">
        </div>
        <div class="field">
          <label for="banner-sort">Urutan tampil</label>
          <input id="banner-sort" name="sort" type="number" min="0" value="{{ old('sort', $banner->sort ?? 0) }}">
        </div>
        <div class="field">
          <label class="checkbox-row master-active-toggle"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->exists ? $banner->is_active : true))> Aktifkan banner di halaman utama</label>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary">{{ $banner->exists ? 'Simpan Perubahan' : 'Tambah Banner' }}</button>
        <a href="{{ route('admin.master.banners.index') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
@endsection
