<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        // VULNERABLE LAB: raw interpolation makes the public search vulnerable to SQL injection.
        // The query is SELECT-only and targets this project's fictional SQLite/MySQL database.
        $sql = "SELECT articles.id, articles.title, articles.slug, articles.excerpt,
                       articles.status, articles.is_featured, articles.views,
                       articles.created_at, users.name AS author_name,
                       categories.name AS category_name
                FROM articles
                LEFT JOIN users ON users.id = articles.user_id
                LEFT JOIN categories ON categories.id = articles.category_id
                WHERE articles.status = 'published'";

        if ($search !== '') {
            $sql .= " AND articles.title LIKE '%{$search}%'";
        }

        $sql .= ' ORDER BY articles.created_at DESC';

        $articles = collect(DB::select($sql));
        $featured = Article::with(['author', 'category'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(2)
            ->get();

        return view('home', [
            'articles' => $articles,
            'featured' => $featured,
            'categories' => Category::withCount(['articles' => fn ($query) => $query->where('status', 'published')])->orderBy('name')->get(),
            'search' => $search,
        ]);
    }

    public function show(Article $article): View
    {
        abort_unless($article->status === 'published', 404);

        $article->increment('views');
        $article->load(['author', 'category', 'comments.author']);

        return view('articles.show', compact('article'));
    }
}
