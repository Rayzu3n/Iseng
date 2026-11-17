@extends('layouts.app')
@section('title', 'Artikel Saya')
@section('content')
<section class="page-heading"><div><div class="eyebrow">PENGELOLAAN KONTEN</div><h1>Artikel Saya</h1><p>Kelola draf dan artikel yang sudah diterbitkan.</p></div><a class="button button-primary" href="{{ route('articles.create') }}">Artikel baru +</a></section>
<section class="panel">
    @forelse($articles as $article)
        <div class="manage-row article-manage-row">
            <div class="manage-copy"><div class="article-meta"><span>{{ $article->category?->name ?? 'Tanpa kategori' }}</span><span>·</span><span class="status-label status-{{ $article->status }}">{{ $article->status === 'published' ? 'Terbit' : 'Draf' }}</span>@if($article->is_featured)<span>· UNGGULAN</span>@endif</div><strong>{{ $article->title }}</strong><small>{{ $article->updated_at->translatedFormat('d M Y · H:i') }}</small></div>
            <div class="manage-actions">
                <a class="button button-small button-quiet" href="{{ route('articles.edit', $article) }}">Edit</a>
                @if($article->status === 'published')<a class="button button-small button-quiet" href="{{ route('articles.show', $article->slug) }}">Lihat</a>@endif
                <form method="POST" action="{{ route('articles.destroy', $article) }}" onsubmit="return confirm('Hapus artikel ini?')">@csrf @method('DELETE')<button class="button button-small button-quiet" type="submit">Hapus</button></form>
            </div>
        </div>
    @empty
        <div class="empty-state">Belum ada artikel. Buat tulisan pertamamu sekarang.</div>
    @endforelse
    <div class="pagination-wrap">{{ $articles->links() }}</div>
</section>
<details class="lab-note"><summary>Latihan keamanan · Otorisasi saat mengedit artikel</summary><p>Daftar artikel dibatasi ke akun yang sedang masuk, tetapi tindakan edit, perbarui, dan hapus belum memeriksa kepemilikan artikel. Periksa ArticleController dan pelajari cara menambahkan policy otorisasi.</p></details>
@endsection
