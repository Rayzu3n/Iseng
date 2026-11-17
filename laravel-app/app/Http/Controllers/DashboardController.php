<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('dashboard', [
            'myArticles' => $user->articles()->latest()->take(5)->get(),
            'articleCount' => $user->articles()->count(),
            'commentCount' => $user->comments()->count(),
            'publishedCount' => $user->articles()->where('status', 'published')->count(),
            'categoryCount' => Category::count(),
            'recentArticles' => Article::with('author')->latest()->take(5)->get(),
            'recentComments' => Comment::with(['author', 'article'])->latest()->take(5)->get(),
        ]);
    }
}
