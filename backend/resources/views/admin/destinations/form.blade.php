@extends('admin.layouts.app')
@section('title', $destination->exists ? 'Edit Destinasi' : 'Tambah Destinasi')
@section('page', $destination->exists ? 'Edit Destinasi' : 'Tambah Destinasi')
@section('actions')
  <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline">Kembali ke daftar</a>
@endsection

@section('content')
  <div class="tour-editor-header">
    <div>
      <span class="eyebrow">Destination management</span>
      <h1>{{ $destination->exists ? 'Edit destinasi' : 'Buat destinasi baru' }}</h1>
      <p>Kelola informasi destinasi, visual, dan paket tour yang tersedia.</p>
    </div>
    <span class="tour-editor-state">{{ $destination->exists ? 'Mode edit' : 'Draft baru' }}</span>
  </div>

  <form method="POST" action="{{ $destination->exists ? route('admin.destinations.update', $destination) : route('admin.destinations.store') }}" enctype="multipart/form-data">
    @csrf
    @if ($destination->exists) @method('PUT') @endif
    <div class="tour-editor-layout">
      <main class="tour-editor-main">
        <section class="editor-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">01 · Informasi utama</span><h2>Identitas destinasi</h2><p>Informasi yang tampil pada kartu dan halaman destinasi.</p></div></div>
          <div class="form-grid">
            <div class="field full"><label for="destination-name">Nama destinasi *</label><input id="destination-name" name="name" value="{{ old('name', $destination->name) }}" placeholder="Contoh: Bali" required></div>
            <div class="field"><label for="destination-country">Negara</label><input id="destination-country" name="country" value="{{ old('country', $destination->country) }}" placeholder="Contoh: Indonesia"></div>
            <div class="field"><label for="destination-type">Tipe</label><select id="destination-type" name="type"><option value="Domestik" @selected(old('type',$destination->type)==='Domestik')>Domestik</option><option value="Mancanegara" @selected(old('type',$destination->type)==='Mancanegara')>Mancanegara</option></select></div>
            <div class="field"><label for="destination-slug">Slug <span class="field-help">Opsional</span></label><input id="destination-slug" name="slug" value="{{ old('slug', $destination->slug) }}" placeholder="otomatis dari nama"></div>
            <div class="field"><label for="destination-sort">Urutan tampil</label><input id="destination-sort" type="number" name="sort" value="{{ old('sort', $destination->sort ?? 0) }}" min="0"></div>
          </div>
        </section>

        <section class="editor-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">02 · Konten</span><h2>Deskripsi destinasi</h2><p>Ceritakan pengalaman dan karakter destinasi kepada customer.</p></div></div>
          <div class="field editor-field-last"><label for="destination-description">Deskripsi</label><textarea id="destination-description" name="description" rows="9" placeholder="Tulis deskripsi destinasi...">{{ old('description', $destination->description) }}</textarea></div>
        </section>
      </main>

      <aside class="tour-editor-side">
        <section class="editor-card editor-side-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">Media</span><h2>Visual destinasi</h2></div></div>
          <div class="field"><label>Gambar utama</label><input type="file" name="image" accept="image/*">@if(!empty($destination->image))<div class="editor-current-image"><img src="{{ \App\Support\Media::url($destination->image) }}"><label class="checkbox-row"><input type="checkbox" name="remove_image" value="1"> Hapus gambar</label></div>@endif</div>
          <div class="field editor-field-last">
            <label for="destination-gallery-input">Galeri <span class="field-help">Multi-upload</span></label>
            <input id="destination-gallery-input" type="file" name="gallery[]" accept="image/*" multiple>
            <div id="destination-gallery-preview" class="gallery-thumbs gallery-preview" aria-live="polite"></div>
            @if (!empty($gallery))
              <div class="gallery-existing-label">Galeri tersimpan</div>
              <div class="gallery-thumbs gallery-existing">
                @foreach ($gallery as $u)
                  <div class="g-item"><img src="{{ $u }}" alt="Thumbnail galeri"><button type="button" class="gallery-remove-existing" aria-label="Hapus gambar galeri">×</button><label class="checkbox-row"><input type="checkbox" name="keep_gallery[]" value="{{ $u }}" checked> Simpan</label></div>
                @endforeach
              </div>
            @endif
          </div>
        </section>

        <section class="editor-card editor-side-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">Relasi</span><h2>Paket tour</h2><p>Pilih paket yang tersedia di destinasi ini.</p></div></div>
          <div class="destination-checklist">@foreach ($tours as $t)<label class="checkbox-row"><input type="checkbox" name="tour_ids[]" value="{{ $t->id }}" @checked($destination->tours?->contains($t->id))> <span>{{ $t->title }}</span></label>@endforeach</div>
        </section>

        <div class="editor-actions"><button class="btn btn-primary">{{ $destination->exists ? 'Simpan perubahan' : 'Buat destinasi' }}</button><a href="{{ route('admin.destinations.index') }}" class="btn btn-outline">Batal</a></div>
      </aside>
    </div>
  </form>
@endsection

@push('scripts')
<script>
  const galleryInput = document.querySelector('#destination-gallery-input')
  const galleryPreview = document.querySelector('#destination-gallery-preview')
  let galleryFiles = []
  const syncGalleryInput = () => { const transfer = new DataTransfer(); galleryFiles.forEach((file) => transfer.items.add(file)); galleryInput.files = transfer.files }
  const renderGalleryPreview = () => {
    galleryPreview.innerHTML = ''
    galleryFiles.forEach((file, index) => {
      const item = document.createElement('div'); item.className = 'g-item gallery-new-item'
      const image = document.createElement('img'); image.src = URL.createObjectURL(file); image.alt = file.name
      const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'gallery-remove-existing'; remove.setAttribute('aria-label', `Hapus ${file.name}`); remove.textContent = '×'
      remove.addEventListener('click', () => { galleryFiles.splice(index, 1); syncGalleryInput(); renderGalleryPreview() })
      const name = document.createElement('span'); name.className = 'gallery-file-name'; name.textContent = file.name
      item.append(image, remove, name); galleryPreview.appendChild(item)
    })
  }
  galleryInput.addEventListener('change', () => { galleryFiles = [...galleryInput.files]; renderGalleryPreview() })
  document.querySelectorAll('.gallery-existing .gallery-remove-existing').forEach((button) => {
    button.addEventListener('click', () => { const item = button.closest('.g-item'); item.querySelector('input[name="keep_gallery[]"]').checked = false; item.classList.add('is-removed') })
  })
</script>
@endpush
