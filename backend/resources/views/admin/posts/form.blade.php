@extends('admin.layouts.app')
@section('title', $post->exists ? 'Edit Artikel' : 'Tulis Artikel')
@section('page', $post->exists ? 'Edit Artikel' : 'Tulis Artikel')

@section('content')
  <div class="card">
    <form method="POST" action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}" enctype="multipart/form-data">
      @csrf
      @if ($post->exists) @method('PUT') @endif
      <div class="form-grid">
        <div class="field full"><label>Judul *</label><input name="title" value="{{ old('title', $post->title) }}" required></div>
        <div class="field"><label>Kategori</label><input name="category" value="{{ $post->category }}" placeholder="cth. Tips Travel"></div>
        <div class="field"><label>Tanggal Terbit</label><input type="date" name="published_at" value="{{ optional($post->published_at)->format('Y-m-d') }}"></div>
        <div class="field"><label>Penulis</label><input name="author" value="{{ $post->author }}"></div>
        <div class="field"><label>Status</label>
          <select name="status">
            <option value="published" @selected(old('status',$post->status)==='published')>Published</option>
            <option value="draft" @selected(old('status',$post->status)==='draft')>Draft</option>
          </select>
        </div>
        <div class="field full"><label>Excerpt / Ringkasan</label><textarea name="excerpt" rows="3">{{ $post->excerpt }}</textarea></div>
        <div class="field full"><label>Isi Artikel (1 paragraf per baris, kosongkan baris antar paragraf)</label>
          <textarea name="body-dummy" id="body-textarea" rows="10" placeholder="Paragraf 1&#10;&#10;Paragraf 2">{{ $post->body_paragraphs ? implode("\n\n", $post->body_paragraphs) : '' }}</textarea>
        </div>
        <div class="field"><label>Cover</label><input type="file" name="cover" accept="image/*">
          @if(!empty($post->cover))<p class="muted"><img class="thumb" src="{{ \App\Support\Media::url($post->cover) }}"></p>
          <label class="checkbox-row"><input type="checkbox" name="remove_cover" value="1"> Hapus cover</label>@endif
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>

  <script>
    // Convert textarea paragraphs -> hidden JSON field before submit
    document.querySelector('form').addEventListener('submit', function () {
      var raw = document.getElementById('body-textarea').value;
      var paragraphs = raw.split(/\n\s*\n/).map(function (p) { return p.trim(); }).filter(Boolean);
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'body_paragraphs';
      input.value = JSON.stringify(paragraphs);
      this.appendChild(input);
    });
  </script>
@endsection