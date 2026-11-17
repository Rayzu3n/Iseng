<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(Request $request, Article $article): RedirectResponse
    {
        abort_unless($article->status === 'published', 404);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Comment::create([
            'article_id' => $article->id,
            'user_id' => auth()->id(),
            'body' => $data['body'],
        ]);

        return redirect()->route('articles.show', $article)->with('success', 'Komentar berhasil dikirim.');
    }
}
