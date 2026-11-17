<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        // VULNERABLE LAB: the route checks authentication but not the admin role.
        return view('admin.index', [
            'users' => User::withCount(['articles', 'comments'])->orderBy('created_at')->get(),
            'articleCount' => Article::count(),
            'publishedCount' => Article::where('status', 'published')->count(),
            'draftCount' => Article::where('status', 'draft')->count(),
            'commentCount' => Comment::count(),
            'categoryCount' => Category::count(),
        ]);
    }
}
