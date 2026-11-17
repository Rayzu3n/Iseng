@extends('layouts.app')
@section('title', 'Jelajahi')
@section('content')
<section class="hero hero-home">
    <div class="eyebrow">KOMUNITAS UNTUK BERBAGI IDE</div>
    <h1>Cerita yang layak<br><span>untuk dipikirkan.</span></h1>
    <p>Temukan catatan, tutorial, dan sudut pandang dari komunitas NusaBoard. Jelajahi topik, temukan ide baru, atau terbitkan tulisanmu sendiri.</p>
    <div class="hero-meta"><span class="status-dot"></span> JURNAL KOMUNITAS <span class="meta-divider"></span> DIBANGUN DENGAN LARAVEL</div>
</section>
<section class="feature-grid">
    @foreach($featured as $feature)
        <a class="feature-card" href="{{ route('articles.show', $feature->slug) }}">
            <span class="eyebrow">ARTIKEL UNGGULAN · {{ $feature->category?->name ?? 'Komunitas' }}</span>
            <h2>{{ $feature->title }}</h2><p>{{ $feature->excerpt }}</p><span class="read-more">Baca artikel ↗</span>
        </a>
    @endforeach
</section>
<section class="content-layout">
    <div class="content-main">
        <div class="section-heading">
            <div><div class="eyebrow">TERBARU</div><h2>{{ $search !== '' ? 'Hasil pencarian' : 'Tulisan terbaru komunitas' }}</h2></div>
            @auth<a class="button button-primary" href="{{ route('articles.create') }}">Tulis artikel +</a>@endauth
        </div>
        <form class="search-form" method="GET" action="{{ route('home') }}">
            <span class="search-icon">⌕</span>
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari artikel berdasarkan judul..." aria-label="Cari artikel">
            <button type="submit">Cari</button>
        </form>
        @if($search !== '')<div class="search-context">Menampilkan hasil untuk <strong>{{ $search }}</strong> · {{ $articles->count() }} hasil</div>@endif
        <div class="article-list">
            @forelse($articles as $article)
                <article class="article-row"><div class="article-row-main">
                    <div class="article-meta"><span>{{ $article->category_name ?? 'Komunitas' }}</span><span>·</span><span>{{ \Illuminate\Support\Carbon::parse($article->created_at)->translatedFormat('d M Y') }}</span></div>
                    <h3><a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a></h3>
                    <p>{{ $article->excerpt }}</p><div class="byline">Oleh {{ $article->author_name ?? 'Anggota komunitas' }} <span>·</span> {{ number_format($article->views) }} kali dibaca</div>
                </div>@if($article->is_featured)<span class="tag tag-featured">UNGGULAN</span>@endif</article>
            @empty
                <div class="empty-state"><strong>Artikel tidak ditemukan.</strong><p>Coba kata kunci lain atau jelajahi kategori yang tersedia.</p></div>
            @endforelse
        </div>
        <details class="lab-note"><summary>Catatan lab keamanan · Pengolahan kueri pencarian</summary>
            <p>Endpoint pencarian publik sengaja menyisipkan teks pencarian langsung ke pernyataan SQL mentah di HomeController.php. Periksa cara kueri dibuat dan bandingkan dengan query builder Laravel yang menggunakan parameter. Kueri latihan ini hanya membaca data contoh lokal.</p>
        </details>
    </div>
    <aside class="sidebar">
        <div class="sidebar-card"><div class="eyebrow">JELAJAHI TOPIK</div><h3>Telusuri kategori</h3>
            @foreach($categories as $category)<div class="category-row"><span>{{ $category->name }}</span><span>{{ $category->articles_count }}</span></div>@endforeach
        </div>
        <div class="sidebar-card sidebar-dark"><div class="eyebrow">LANGKAH BERIKUTNYA</div><h3>Punya sesuatu untuk dibagikan?</h3>
            <p>Ubah catatan berguna, masalah yang berhasil kamu pecahkan, atau sudut pandang baru menjadi tulisan untuk komunitas.</p>
            <a href="{{ auth()->check() ? route('articles.create') : route('register') }}" class="text-link">Mulai menulis ↗</a>
        </div>
        <div class="sidebar-card lab-card"><div class="eyebrow">MODE LATIHAN LOKAL</div><h3>Baca kodenya. Periksa kepercayaannya.</h3>
            <p>NusaBoard memiliki beberapa kesalahan keamanan yang disengaja untuk ditinjau dan dipelajari secara lokal.</p>
            <a class="text-link" href="https://github.com/Rayzu3n/Iseng/tree/main/laravel-app" target="_blank" rel="noreferrer">Kode sumber proyek ↗</a>
        </div>
    </aside>
</section>
@endsection
