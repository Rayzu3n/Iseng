<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@nusaboard.test'],
            ['name' => 'Nusa Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        $editor = User::updateOrCreate(
            ['email' => 'editor@nusaboard.test'],
            ['name' => 'Alya Editor', 'password' => Hash::make('password'), 'role' => 'editor']
        );

        $member = User::updateOrCreate(
            ['email' => 'member@nusaboard.test'],
            ['name' => 'Raka Member', 'password' => Hash::make('password'), 'role' => 'member']
        );

        $tech = Category::updateOrCreate(
            ['slug' => 'technology'],
            ['name' => 'Technology', 'description' => 'Software, devices, and the digital world.']
        );
        $games = Category::updateOrCreate(
            ['slug' => 'gaming'],
            ['name' => 'Gaming', 'description' => 'Games, communities, and interactive media.']
        );
        $community = Category::updateOrCreate(
            ['slug' => 'community'],
            ['name' => 'Community', 'description' => 'Stories and ideas from the community.']
        );

        $articles = [
            [
                'user_id' => $editor->id, 'category_id' => $tech->id,
                'title' => 'Why small projects teach big lessons',
                'excerpt' => 'A practical look at learning through projects, iteration, and careful review.',
                'body' => "The best way to understand a system is to build one, inspect its behavior, and improve it. Small projects create room to experiment without losing sight of the fundamentals.",
                'status' => 'published', 'is_featured' => true,
            ],
            [
                'user_id' => $admin->id, 'category_id' => $games->id,
                'title' => 'Building healthier gaming communities',
                'excerpt' => 'Good community spaces need thoughtful rules, moderation, and trust.',
                'body' => "A community is more than its software. Clear expectations, useful moderation tools, and respectful discussion help people enjoy the space together.",
                'status' => 'published', 'is_featured' => true,
            ],
            [
                'user_id' => $member->id, 'category_id' => $community->id,
                'title' => 'My first weekend learning Laravel',
                'excerpt' => 'Notes from setting up routes, models, migrations, and Blade views.',
                'body' => "Starting with the request lifecycle made the framework easier to understand. Routes connect requests to controllers, models represent data, and views shape the response.",
                'status' => 'published', 'is_featured' => false,
            ],
            [
                'user_id' => $editor->id, 'category_id' => $tech->id,
                'title' => 'A draft about secure defaults',
                'excerpt' => 'This is intentionally unpublished sample content for the local lab.',
                'body' => 'Draft content is not supposed to appear in public search results.',
                'status' => 'draft', 'is_featured' => false,
            ],
        ];

        foreach ($articles as $data) {
            $slug = Str::slug($data['title']);
            Article::updateOrCreate(
                ['slug' => $slug],
                [...$data, 'slug' => $slug, 'published_at' => $data['status'] === 'published' ? now() : null]
            );
        }

        $firstArticle = Article::where('slug', 'why-small-projects-teach-big-lessons')->first();
        if ($firstArticle && $firstArticle->comments()->doesntExist()) {
            Comment::create([
                'article_id' => $firstArticle->id,
                'user_id' => $member->id,
                'body' => 'A useful reminder to keep projects small and finishable.',
            ]);
            Comment::create([
                'article_id' => $firstArticle->id,
                'user_id' => $editor->id,
                'body' => 'Reviewing the result is just as important as writing the first version.',
            ]);
        }
    }
}
