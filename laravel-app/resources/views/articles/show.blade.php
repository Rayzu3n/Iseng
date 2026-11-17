@extends('layouts.app')
@section('title', $article->title)
@section('content')
<article class="story-page">
    <a class="back-link" href="{{ route('home') }}">← Back to stories</a>
    <div class="story-header">
        <div class="article-meta"><span>{{ $article->category?->name ?? 'Community' }}</span><span>·</span><span>{{ optional($article->published_at)->format('M d, Y') ?? $article->created_at->format('M d, Y') }}</span><span>·</span><span>{{ number_format($article->views) }} reads</span></div>
        <h1>{{ $article->title }}</h1>
        <p class="story-excerpt">{{ $article->excerpt }}</p>
        <div class="byline">Written by <strong>{{ $article->author->name }}</strong></div>
    </div>
    <div class="story-body">{!! nl2br(e($article->body)) !!}</div>
    <section class="comments-section">
        <div class="eyebrow">COMMUNITY DISCUSSION</div><h2>{{ $article->comments->count() }} comments</h2>
        @auth
            <form method="POST" action="{{ route('comments.store', $article) }}" class="comment-form">
                @csrf
                <textarea class="input textarea" name="body" rows="3" maxlength="2000" placeholder="Add to the conversation..." required></textarea>
                <button class="button button-primary" type="submit">Post comment ↗</button>
            </form>
        @else
            <p class="muted">Please <a href="{{ route('login') }}">sign in</a> to join the discussion.</p>
        @endauth
        @forelse($article->comments as $comment)
            <div class="comment-item">
                <span class="avatar">{{ strtoupper(substr($comment->author->name, 0, 1)) }}</span>
                <div><div class="comment-author">{{ $comment->author->name }} <span>· {{ $comment->created_at->diffForHumans() }}</span></div>
                <div class="comment-content">{!! $comment->body !!}</div></div>
            </div>
        @empty
            <div class="empty-state">No comments yet. Start the conversation.</div>
        @endforelse
    </section>
    <details class="lab-note"><summary>Security exercise · Stored XSS</summary><p>Comments are persisted in the database and rendered with Blade's unescaped output syntax. Compare this with the escaped output syntax used for the article body. The flaw is limited to this local fictional app.</p></details>
</article>
@endsection
