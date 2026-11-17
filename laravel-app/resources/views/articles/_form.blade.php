@csrf
<div class="form-grid">
    <div class="form-main">
        <label class="field-label" for="title">Article title</label>
        <input class="input" id="title" name="title" value="{{ old('title', $article->title ?? '') }}" maxlength="180" required>
        <label class="field-label" for="excerpt">Short summary</label>
        <textarea class="input textarea" id="excerpt" name="excerpt" rows="3" maxlength="500">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
        <label class="field-label" for="body">Article content</label>
        <textarea class="input textarea editor-textarea" id="body" name="body" rows="14" required>{{ old('body', $article->body ?? '') }}</textarea>
    </div>
    <aside class="form-side">
        <label class="field-label" for="category_id">Category</label>
        <select class="input" name="category_id" id="category_id">
            <option value="">Choose a category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $article->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <label class="field-label" for="status">Publication status</label>
        <select class="input" name="status" id="status">
            <option value="draft" @selected(old('status', $article->status ?? 'draft') === 'draft')>Draft</option>
            <option value="published" @selected(old('status', $article->status ?? 'draft') === 'published')>Published</option>
        </select>
        <label class="check-row"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $article->is_featured ?? false))> Mark as featured</label>
        <div class="form-side-note"><strong>Publishing note</strong><p>New stories can be saved as drafts or published. This local project deliberately includes a permissions flaw around publishing controls.</p></div>
        <button class="button button-primary button-wide" type="submit">{{ $submitLabel ?? 'Save article' }} ↗</button>
        <a class="button button-quiet button-wide" href="{{ route('articles.index') }}">Cancel</a>
    </aside>
</div>
