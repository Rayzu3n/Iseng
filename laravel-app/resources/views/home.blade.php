@extends('layouts.app')

@section('title', 'Explore')

@section('content')
<section class="hero hero-home">
    <div class="eyebrow">A COMMUNITY FOR IDEAS</div>
    <h1>Stories worth<br><span>thinking about.</span></h1>
    <p>Discover notes, tutorials, and perspectives from the NusaBoard community. Browse a topic, find a new idea, or publish something of your own.</p>
    <div class="hero-meta"><span class="status-dot"></span> COMMUNITY JOURNAL <span class="meta-divider"></span> BUILT WITH LARAVEL</div>
</section>

<section class="feature-grid">
    @foreach($featured as $feature)
        <a class="feature-card" href="{{ route('articles.show', $feature->slug) }}">
            <span class="eyebrow">FEATURED STORY · {{ $feature->category?->name ?? 'Community' }}</span>
            <h2>{{ $feature->title }}</h2>
            <p>{{ $feature->excerpt }}</p>
            <span class="read-more">Read story ↗</span>
        </a>
    @endforeach
</section>

<section class="content-layout">
    <div class="content-main">
        <div class="section-heading">
            <div><div class="eyebrow">THE LATEST</div><h2>{{ $search !== '' ? 'Search results' : 'Fresh from the community' }}</h2></div>
            @auth<a class="button button-primary" href="{{ route('articles.create') }}">Write an article +</a>@endauth
        </div>

        <form class="search-form" method="GET" action="{{ route('home') }}">
            <span class="search-icon">⌕</span>
            <input type="search" name="q" value="{{ $search }}" placeholder="Search stories by title..." aria-label="Search stories">
            <button type="submit">Search</button>
        </form>

        @if($search !== '')
            <div class="search-context">Showing results for <strong>{{ $search }}</strong> · {{ $articles->count() }} result(s)</div>
        @endif

        <div class="article-list">
            @forelse($articles as $article)
                <article class="article-row">
                    <div class="article-row-main">
                        <div class="article-meta"><span>{{ $article->category_name ?? 'Community' }}</span><span>·</span><span>{{ \Illuminate\Support\Carbon::parse($article->created_at)->format('M d, Y') }}</span></div>
                        <h3><a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a></h3>
                        <p>{{ $article->excerpt }}</p>
                        <div class="byline">By {{ $article->author_name ?? 'Community member' }} <span>·</span> {{ number_format($article->views) }} reads</div>
                    </div>
                    @if($article->is_featured)<span class="tag tag-featured">FEATURED</span>@endif
                </article>
            @empty
                <div class="empty-state"><strong>No stories found.</strong><p>Try another search or explore a category.</p></div>
            @endforelse
        </div>

        <details class="lab-note">
            <summary>Security lab note · Search query handling</summary>
            <p>The public search endpoint intentionally interpolates the search string into a raw SQL statement in <code>HomeController.php</code>. Inspect the query construction and compare it with Laravel's parameterized query builder. This lab query is SELECT-only and operates on local sample content.</p>
        </details>
    </div>

    <aside class="sidebar">
        <div class="sidebar-card">
            <div class="eyebrow">EXPLORE TOPICS</div>
            <h3>Browse categories</h3>
            @foreach($categories as $category)
                <div class="category-row"><span>{{ $category->name }}</span><span>{{ $category->articles_count }}</span></div>
            @endforeach
        </div>
        <div class="sidebar-card sidebar-dark">
            <div class="eyebrow">YOUR NEXT CHAPTER</div>
            <h3>Have something to share?</h3>
            <p>Turn a useful note, a solved problem, or a fresh perspective into a story for the community.</p>
            <a href="{{ auth()->check() ? route('articles.create') : route('register') }}" class="text-link">Start writing ↗</a>
        </div>
        <div class="sidebar-card lab-card">
            <div class="eyebrow">LOCAL TRAINING MODE</div>
            <h3>Read the code. Question the trust.</h3>
            <p>NusaBoard includes deliberate security mistakes for source review and local practice.</p>
            <a class="text-link" href="https://github.com/Rayzu3n/Iseng/tree/feature/laravel-realistic-vulnerability-lab/laravel-app" target="_blank" rel="noreferrer">Project source ↗</a>
        </div>
    </aside>
</section>
@endsection
