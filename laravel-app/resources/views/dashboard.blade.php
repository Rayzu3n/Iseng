@extends('layouts.app')
@section('title', 'Dasbor')
@section('content')
<section class="page-heading">
    <div><div class="eyebrow">RUANG KERJA KAMU</div><h1>Senang melihatmu, {{ auth()->user()->name }}.</h1><p>Berikut ringkasan aktivitas akun NusaBoard kamu.</p></div>
    <a class="button button-primary" href="{{ route('articles.create') }}">Buat artikel +</a>
</section>
<div class="stats-grid">
    <div class="stat-card"><span>ARTIKEL SAYA</span><strong>{{ $articleCount }}</strong><small>Artikel dan draf</small></div>
    <div class="stat-card"><span>DITERBITKAN</span><strong>{{ $publishedCount }}</strong><small>Dapat dibaca pengunjung</small></div>
    <div class="stat-card"><span>KOMENTAR SAYA</span><strong>{{ $commentCount }}</strong><small>Kontribusi diskusi</small></div>
    <div class="stat-card"><span>TOPIK</span><strong>{{ $categoryCount }}</strong><small>Kategori komunitas</small></div>
</div>
<div class="dashboard-grid">
    <section class="panel">
        <div class="section-heading"><div><div class="eyebrow">KONTEN KAMU</div><h2>Artikel terbaru</h2></div><a class="text-link" href="{{ route('articles.index') }}">Lihat semua ↗</a></div>
        @forelse($myArticles as $article)
            <div class="manage-row"><div><strong>{{ $article->title }}</strong><small>{{ $article->status === 'published' ? 'Terbit' : 'Draf' }} · {{ $article->updated_at->translatedFormat('d M Y') }}</small></div>
                <a class="button button-small button-quiet" href="{{ route('articles.edit', $article) }}">Edit</a></div>
        @empty
            <div class="empty-state">Kamu belum menulis artikel. Mulailah dengan catatan singkat.</div>
        @endforelse
    </section>
    <aside class="panel"><div class="eyebrow">RINGKASAN AKUN</div><h2>Akses akun</h2>
        <div class="role-box"><span class="avatar avatar-large">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'admin' ? 'ADMINISTRATOR' : (auth()->user()->role === 'editor' ? 'EDITOR' : 'ANGGOTA') }}</small></div></div>
        <p class="muted">Peran akun menentukan tindakan pengelolaan yang seharusnya tersedia.</p>
        <details class="lab-note"><summary>Latihan keamanan · Rute admin</summary><p>Navigasi menyembunyikan tautan admin dari anggota biasa, tetapi periksa middleware rutenya. Menyembunyikan tautan bukanlah mekanisme otorisasi.</p><a class="text-link" href="{{ url('/admin') }}">Buka /admin secara langsung ↗</a></details>
    </aside>
</div>
<section class="panel panel-wide"><div class="section-heading"><div><div class="eyebrow">AKTIVITAS KOMUNITAS</div><h2>Baru saja diterbitkan</h2></div><a class="text-link" href="{{ route('home') }}">Jelajahi artikel ↗</a></div>
    <div class="compact-list">@foreach($recentArticles as $article)
        <div class="compact-row"><span class="compact-category">{{ $article->category?->name ?? 'Komunitas' }}</span><strong>{{ $article->title }}</strong><small>oleh {{ $article->author->name }}</small></div>
    @endforeach</div>
</section>
@endsection
