@extends('admin.layouts.app')
@section('title', $tour->exists ? 'Edit Tour' : 'Tambah Tour')
@section('page', $tour->exists ? 'Edit Tour' : 'Tambah Tour')
@section('actions')
  <a href="{{ route('admin.tours.index') }}" class="btn btn-outline">Kembali ke daftar</a>
@endsection

@section('content')
  <div class="tour-editor-header">
    <div>
      <span class="eyebrow">Tour management</span>
      <h1>{{ $tour->exists ? 'Edit paket tour' : 'Buat paket tour baru' }}</h1>
      <p>Susun informasi paket, jadwal, dan media agar siap ditampilkan di website.</p>
    </div>
    <span class="tour-editor-state">{{ $tour->exists ? 'Mode edit' : 'Draft baru' }}</span>
  </div>

  <form id="tour-form" method="POST" action="{{ $tour->exists ? route('admin.tours.update', $tour) : route('admin.tours.store') }}" enctype="multipart/form-data">
    @csrf
    @if ($tour->exists) @method('PUT') @endif
    <div class="tour-editor-layout">
      <main class="tour-editor-main">
        <section class="editor-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">01 · Informasi utama</span><h2>Identitas paket</h2><p>Informasi yang pertama kali dilihat customer.</p></div></div>
          <div class="form-grid">
            <div class="field full"><label for="tour-title">Judul tour *</label><input id="tour-title" name="title" value="{{ old('title', $tour->title) }}" placeholder="Contoh: Bromo & Ijen Sunrise Adventure" required></div>
            <div class="field"><label for="tour-category">Kategori *</label><select id="tour-category" name="category" required><option value="domestik" @selected(old('category',$tour->category)==='domestik')>Domestik</option><option value="mancanegara" @selected(old('category',$tour->category)==='mancanegara')>Mancanegara</option></select></div>
            <div class="field"><label for="tour-location">Lokasi</label><input id="tour-location" name="location" value="{{ old('location', $tour->location) }}" placeholder="Contoh: Jawa Timur"></div>
            <div class="field"><label for="tour-duration">Durasi</label><input id="tour-duration" name="duration" value="{{ old('duration', $tour->duration) }}" placeholder="Contoh: 3 Hari 2 Malam"></div>
            <div class="field"><label for="tour-price">Harga mulai (Rp)</label><input id="tour-price" type="number" name="price_start" value="{{ old('price_start', $tour->price_start) }}" min="0" placeholder="3750000"></div>
            <div class="field full"><label for="tour-tagline">Tagline</label><input id="tour-tagline" name="tagline" value="{{ old('tagline', $tour->tagline) }}" placeholder="Kalimat singkat yang menjelaskan pengalaman tour"></div>
          </div>
        </section>

        <section class="editor-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">02 · Konten</span><h2>Ceritakan paketnya</h2><p>Gunakan deskripsi dan highlight untuk membangun ekspektasi yang jelas.</p></div></div>
          <div class="field"><label>Deskripsi paket</label><div id="description-editor">{!! old('description', $tour->description) !!}</div><textarea id="description-input" name="description" hidden>{{ old('description', $tour->description) }}</textarea></div>
          <div class="field editor-field-last"><label for="tour-highlights">Highlight <span class="field-help">Satu poin per baris</span></label><textarea id="tour-highlights" name="highlights" rows="5" placeholder="Sunrise point terbaik\nTransportasi selama tour\nGuide berpengalaman">{{ old('highlights', $tour->highlights ? implode("\n", $tour->highlights) : '') }}</textarea></div>
        </section>

        <section class="editor-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">03 · Itinerary</span><h2>Rencana perjalanan</h2><p>Tambahkan detail aktivitas untuk setiap hari perjalanan.</p></div></div>
          <div id="itinerary-items">
            @php($itineraryItems = old('itinerary', $itinerary ?? $tour->itinerary ?? []))
            @if (empty($itineraryItems)) @php($itineraryItems = [['day' => '1', 'route' => '', 'details' => '']]) @endif
            @foreach ($itineraryItems as $index => $item)
              <div class="itinerary-form-row">
                <div class="itinerary-form-head"><strong>Hari {{ $index + 1 }}</strong><button type="button" class="btn-ghost remove-itinerary" @if(count($itineraryItems) === 1) hidden @endif>Hapus</button></div>
                <div class="itinerary-form-fields"><input name="itinerary[{{ $index }}][day]" value="{{ is_array($item) ? ($item['day'] ?? $index + 1) : $index + 1 }}" placeholder="1" required><input name="itinerary[{{ $index }}][route]" value="{{ is_array($item) ? ($item['route'] ?? '') : '' }}" placeholder="Contoh: Jakarta - Istanbul" required></div>
                <div class="itinerary-editor">{!! is_array($item) ? ($item['details'] ?? '') : '' !!}</div><textarea class="itinerary-input" name="itinerary[{{ $index }}][details]" hidden>{{ is_array($item) ? ($item['details'] ?? '') : '' }}</textarea>
              </div>
            @endforeach
          </div>
          <button type="button" class="btn btn-outline editor-add-button" id="add-itinerary">+ Tambah hari</button>
        </section>

        <section class="editor-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">04 · Jadwal</span><h2>Jadwal keberangkatan</h2><p>Customer dapat memilih jadwal ini saat melakukan booking.</p></div></div>
          <div class="schedule-help"><strong>Format setiap baris</strong><code>tanggal mulai | tanggal selesai | available/full | harga</code></div>
          <textarea class="schedule-input" name="departure_schedules" rows="6" placeholder="24 Dec 2026 | 28 Dec 2026 | available | 4500000">@if($tour->departure_schedules)@foreach($tour->departure_schedules as $schedule){{ $schedule['start_date'] }} | {{ $schedule['end_date'] }} | {{ $schedule['status'] }} | {{ $schedule['price'] ?? '' }}
@endforeach @endif</textarea>
        </section>
      </main>

      <aside class="tour-editor-side">
        <section class="editor-card editor-side-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">Publikasi</span><h2>Kontrol tampilan</h2></div></div>
          <div class="field"><label for="tour-status">Status *</label><select id="tour-status" name="status" required><option value="published" @selected(old('status',$tour->status)==='published')>Published</option><option value="draft" @selected(old('status',$tour->status)==='draft')>Draft</option></select></div>
          <div class="field"><label for="tour-tag">Badge</label><input id="tour-tag" name="tag" value="{{ old('tag', $tour->tag) }}" placeholder="Promo / Terpopuler"></div>
          <div class="field editor-field-last"><label for="tour-sort">Urutan tampil</label><input id="tour-sort" type="number" name="sort" value="{{ old('sort', $tour->sort ?? 0) }}" min="0"></div>
        </section>

        <section class="editor-card editor-side-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">Media</span><h2>Visual paket</h2></div></div>
          <div class="field"><label>Gambar utama</label><input type="file" name="image" accept="image/*">@if(!empty($tour->image))<div class="editor-current-image"><img src="{{ \App\Support\Media::url($tour->image) }}"><label class="checkbox-row"><input type="checkbox" name="remove_image" value="1"> Hapus gambar</label></div>@endif</div>
          <div class="field editor-field-last">
            <label for="gallery-input">Galeri <span class="field-help">Bisa pilih banyak gambar sekaligus</span></label>
            <input id="gallery-input" type="file" name="gallery[]" accept="image/*" multiple>
            <div id="gallery-preview" class="gallery-thumbs gallery-preview" aria-live="polite"></div>
            @if (!empty($gallery))
              <div class="gallery-existing-label">Galeri tersimpan</div>
              <div class="gallery-thumbs gallery-existing">
                @foreach ($gallery as $u)
                  <div class="g-item">
                    <img src="{{ $u }}" alt="Thumbnail galeri">
                    <button type="button" class="gallery-remove-existing" aria-label="Hapus gambar galeri">×</button>
                    <label class="checkbox-row"><input type="checkbox" name="keep_gallery[]" value="{{ $u }}" checked> Simpan</label>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </section>

        <section class="editor-card editor-side-card">
          <div class="editor-card-heading"><div><span class="editor-kicker">Relasi</span><h2>Destinasi</h2><p>Hubungkan paket dengan halaman destinasi.</p></div></div>
          <div class="destination-checklist">@foreach ($destinations as $d)<label class="checkbox-row"><input type="checkbox" name="destination_ids[]" value="{{ $d->id }}" @checked($tour->destinations?->contains($d->id))> <span>{{ $d->name }}</span></label>@endforeach</div>
        </section>

        <div class="editor-actions"><button class="btn btn-primary">{{ $tour->exists ? 'Simpan perubahan' : 'Buat paket tour' }}</button><a href="{{ route('admin.tours.index') }}" class="btn btn-outline">Batal</a></div>
      </aside>
    </div>
  </form>
@endsection

@push('scripts')
<script>
  const itineraryItems = document.querySelector('#itinerary-items')
  const toolbar = [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline', 'strike'], [{ list: 'ordered' }, { list: 'bullet' }], ['blockquote', 'link'], ['clean']]
  const descriptionInput = document.querySelector('#description-input')
  const descriptionEditor = new Quill('#description-editor', { theme: 'snow', placeholder: 'Tulis deskripsi paket...', modules: { toolbar } })
  let itineraryIndex = itineraryItems.children.length
  const setupItineraryEditor = (row) => {
    const editor = new Quill(row.querySelector('.itinerary-editor'), { theme: 'snow', placeholder: 'Tulis detail perjalanan...', modules: { toolbar } })
    const input = row.querySelector('.itinerary-input')
    editor.on('text-change', () => { input.value = editor.root.innerHTML })
    row.querySelector('.remove-itinerary').addEventListener('click', () => { row.remove(); document.querySelectorAll('.remove-itinerary').forEach((button) => { button.hidden = itineraryItems.children.length === 1 }) })
  }
  document.querySelectorAll('.itinerary-form-row').forEach(setupItineraryEditor)
  document.querySelector('#add-itinerary').addEventListener('click', () => {
    const row = document.createElement('div')
    row.className = 'itinerary-form-row'
    row.innerHTML = `<div class="itinerary-form-head"><strong>Hari ${itineraryItems.children.length + 1}</strong><button type="button" class="btn-ghost remove-itinerary">Hapus</button></div><div class="itinerary-form-fields"><input name="itinerary[${itineraryIndex}][day]" value="${itineraryItems.children.length + 1}" placeholder="1" required><input name="itinerary[${itineraryIndex}][route]" placeholder="Contoh: Jakarta - Istanbul" required></div><div class="itinerary-editor"></div><textarea class="itinerary-input" name="itinerary[${itineraryIndex}][details]" hidden></textarea>`
    itineraryItems.appendChild(row); itineraryIndex += 1; setupItineraryEditor(row); document.querySelectorAll('.remove-itinerary').forEach((button) => { button.hidden = false })
  })
  const galleryInput = document.querySelector('#gallery-input')
  const galleryPreview = document.querySelector('#gallery-preview')
  let galleryFiles = []
  const renderGalleryPreview = () => {
    galleryPreview.innerHTML = ''
    galleryFiles.forEach((file, index) => {
      const item = document.createElement('div')
      item.className = 'g-item gallery-new-item'
      const image = document.createElement('img')
      image.src = URL.createObjectURL(file)
      image.alt = file.name
      const remove = document.createElement('button')
      remove.type = 'button'
      remove.className = 'gallery-remove-existing'
      remove.setAttribute('aria-label', `Hapus ${file.name}`)
      remove.textContent = '×'
      remove.addEventListener('click', () => { galleryFiles.splice(index, 1); syncGalleryInput(); renderGalleryPreview() })
      const name = document.createElement('span')
      name.className = 'gallery-file-name'
      name.textContent = file.name
      item.append(image, remove, name)
      galleryPreview.appendChild(item)
    })
  }
  const syncGalleryInput = () => {
    const transfer = new DataTransfer()
    galleryFiles.forEach((file) => transfer.items.add(file))
    galleryInput.files = transfer.files
  }
  galleryInput.addEventListener('change', () => { galleryFiles = [...galleryInput.files]; renderGalleryPreview() })
  document.querySelectorAll('.gallery-remove-existing').forEach((button) => {
    button.addEventListener('click', () => {
      const item = button.closest('.g-item')
      const checkbox = item.querySelector('input[name="keep_gallery[]"]')
      checkbox.checked = false
      item.classList.add('is-removed')
    })
  })
  document.querySelector('#tour-form').addEventListener('submit', () => { descriptionInput.value = descriptionEditor.root.innerHTML; document.querySelectorAll('.itinerary-form-row').forEach((row) => { const editor = row.querySelector('.ql-editor'); row.querySelector('.itinerary-input').value = editor ? editor.innerHTML : '' }) })
</script>
@endpush
