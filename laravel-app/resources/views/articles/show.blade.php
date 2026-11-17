@extends('layouts.app')
@section('title', $article->title)
@section('content')
<article class="story-page">
    <a class="back-link" href="{{ route('home') }}">← Kembali ke artikel</a>
    <div class="story-header">
        <div class="article-meta"><span>{{ $article->category?->name ?? 'Komunitas' }}</span><span>·</span><span>{{ optional($article->published_at)->translatedFormat('d M Y') ?? $article->created_at->translatedFormat('d M Y') }}</span><span>·</span><span>{{ number_format($article->views) }} kali dibaca</span></div>
        <h1>{{ $article->title }}</h1><p class="story-excerpt">{{ $article->excerpt }}</p>
        <div class="byline">Ditulis oleh <strong>{{ $article->author->name }}</strong></div>
    </div>
    <div class="story-body">{!! nl2br(e($article->body)) !!}</div>
    <section class="comments-section"><div class="eyebrow">DISKUSI KOMUNITAS</div><h2>{{ $article->comments->count() }} komentar</h2>
        @auth
            <form method="POST" action="{{ route('comments.store', $article) }}" class="comment-form">@csrf
                <textarea class="input textarea" name="body" rows="3" maxlength="2000" placeholder="Tulis komentar..." required></textarea>
                <button class="button button-primary" type="submit">Kirim komentar ↗</button>
            </form>
        @else
            <p class="muted">Silakan <a href="{{ route('login') }}">masuk</a> untuk ikut berdiskusi.</p>
        @endauth
        @forelse($article->comments as $comment)
            <div class="comment-item"><span class="avatar">{{ strtoupper(substr($comment->author->name, 0, 1)) }}</span>
                <div><div class="comment-author">{{ $comment->author->name }} <span>· {{ $comment->created_at->diffForHumans() }}</span></div>
                <div class="comment-content">{!! $comment->body !!}</div></div>
            </div>
        @empty
            <div class="empty-state">Belum ada komentar. Jadilah yang pertama memulai diskusi.</div>
        @endforelse
    </section>
    <details class="lab-note"><summary>Latihan keamanan · Stored XSS</summary><p>Komentar disimpan ke database dan ditampilkan menggunakan sintaks Blade tanpa escaping. Bandingkan dengan sintaks output Blade yang sudah di-escape pada isi artikel. Kekurangan ini hanya untuk aplikasi fiktif yang dijalankan secara lokal.</p></details>
</article>
@endsection
