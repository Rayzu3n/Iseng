@csrf
<div class="form-grid">
    <div class="form-main">
        <label class="field-label" for="title">Judul artikel</label>
        <input class="input" id="title" name="title" value="{{ old('title', $article->title ?? '') }}" maxlength="180" required>
        <label class="field-label" for="excerpt">Ringkasan singkat</label>
        <textarea class="input textarea" id="excerpt" name="excerpt" rows="3" maxlength="500">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
        <label class="field-label" for="body">Isi artikel</label>
        <textarea class="input textarea editor-textarea" id="body" name="body" rows="14" required>{{ old('body', $article->body ?? '') }}</textarea>
    </div>
    <aside class="form-side">
        <label class="field-label" for="category_id">Kategori</label>
        <select class="input" name="category_id" id="category_id"><option value="">Pilih kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $article->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <label class="field-label" for="status">Status publikasi</label>
        <select class="input" name="status" id="status">
            <option value="draft" @selected(old('status', $article->status ?? 'draft') === 'draft')>Draf</option>
            <option value="published" @selected(old('status', $article->status ?? 'draft') === 'published')>Terbit</option>
        </select>
        <label class="check-row"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $article->is_featured ?? false))> Tandai sebagai artikel unggulan</label>
        <div class="form-side-note"><strong>Catatan publikasi</strong><p>Tulisan baru dapat disimpan sebagai draf atau langsung diterbitkan. Proyek lokal ini sengaja memiliki celah izin pada pengaturan publikasi.</p></div>
        <button class="button button-primary button-wide" type="submit">{{ $submitLabel ?? 'Simpan artikel' }} ↗</button>
        <a class="button button-quiet button-wide" href="{{ route('articles.index') }}">Batal</a>
    </aside>
</div>
