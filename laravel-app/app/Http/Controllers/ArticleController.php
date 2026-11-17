<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        // The list is scoped to the signed-in user. The edit route below is not.
        $articles = auth()->user()->articles()->with('category')->latest()->paginate(10);

        return view('articles.index', compact('articles'));
    }

    public function create(): View
    {
        return view('articles.create', ['categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:30000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        // VULNERABLE LAB: mass assignment accepts extra fillable fields from the request.
        // A normal member can submit status=is_featured or user_id values not in the UI.
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        $data['published_at'] = ($data['status'] ?? 'draft') === 'published' ? now() : null;

        Article::create($data);

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil dibuat.');
    }

    public function edit(Article $article): View
    {
        // VULNERABLE LAB: no owner or editor policy check (IDOR / broken access control).
        return view('articles.edit', [
            'article' => $article,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:30000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        // VULNERABLE LAB: the record is not authorized and all fillable request values are accepted.
        $data = $request->all();
        if (($data['title'] ?? $article->title) !== $article->title) {
            $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        }
        $article->fill($data);
        if (array_key_exists('status', $data)) {
            $article->published_at = $data['status'] === 'published' ? ($article->published_at ?? now()) : null;
        }
        $article->save();

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        // VULNERABLE LAB: missing ownership/policy enforcement on delete.
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
