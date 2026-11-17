@extends('layouts.app')
@section('title', 'My articles')
@section('content')
<section class="page-heading"><div><div class="eyebrow">CONTENT STUDIO</div><h1>My articles</h1><p>Manage your drafts and published stories.</p></div><a class="button button-primary" href="{{ route('articles.create') }}">New article +</a></section>
<section class="panel">
    @forelse($articles as $article)
        <div class="manage-row article-manage-row">
            <div class="manage-copy"><div class="article-meta"><span>{{ $article->category?->name ?? 'Uncategorized' }}</span><span>·</span><span class="status-label status-{{ $article->status }}">{{ ucfirst($article->status) }}</span>@if($article->is_featured)<span>· FEATURED</span>@endif</div><strong>{{ $article->title }}</strong><small>{{ $article->updated_at->format('M d, Y · H:i') }}</small></div>
            <div class="manage-actions">
                <a class="button button-small button-quiet" href="{{ route('articles.edit', $article) }}">Edit</a>
                @if($article->status === 'published')<a class="button button-small button-quiet" href="{{ route('articles.show', $article->slug) }}">View</a>@endif
                <form method="POST" action="{{ route('articles.destroy', $article) }}" onsubmit="return confirm('Delete this article?')">
                    @csrf
                    @method('DELETE')
                    <button class="button button-small button-quiet" type="submit">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-state">No articles yet. Create your first story.</div>
    @endforelse
    <div class="pagination-wrap">{{ $articles->links() }}</div>
</section>
<details class="lab-note"><summary>Security exercise · Article editing authorization</summary><p>Your list is scoped to your own articles, but the edit, update, and delete actions do not enforce ownership. Review <code>ArticleController</code> and add an authorization policy.</p></details>
@endsection
