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
            ['name' => 'Admin Nusa', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        $editor = User::updateOrCreate(
            ['email' => 'editor@nusaboard.test'],
            ['name' => 'Alya Editor', 'password' => Hash::make('password'), 'role' => 'editor']
        );

        $member = User::updateOrCreate(
            ['email' => 'member@nusaboard.test'],
            ['name' => 'Raka Anggota', 'password' => Hash::make('password'), 'role' => 'member']
        );

        $tech = Category::updateOrCreate(
            ['slug' => 'technology'],
            ['name' => 'Teknologi', 'description' => 'Perangkat lunak, perangkat digital, dan dunia teknologi.']
        );
        $games = Category::updateOrCreate(
            ['slug' => 'gaming'],
            ['name' => 'Game', 'description' => 'Permainan, komunitas, dan media interaktif.']
        );
        $community = Category::updateOrCreate(
            ['slug' => 'community'],
            ['name' => 'Komunitas', 'description' => 'Cerita dan gagasan dari anggota komunitas.']
        );

        $articles = [
            [
                'user_id' => $editor->id, 'category_id' => $tech->id,
                'title' => 'Pelajaran besar dari proyek kecil',
                'excerpt' => 'Cara belajar melalui proyek, proses pengembangan bertahap, dan evaluasi yang cermat.',
                'body' => "Cara terbaik memahami sebuah sistem adalah dengan membangunnya, memeriksa perilakunya, lalu memperbaikinya. Proyek kecil memberi ruang untuk bereksperimen tanpa melupakan dasar-dasarnya.",
                'status' => 'published', 'is_featured' => true,
            ],
            [
                'user_id' => $admin->id, 'category_id' => $games->id,
                'title' => 'Membangun komunitas game yang sehat',
                'excerpt' => 'Komunitas yang baik membutuhkan aturan yang jelas, moderasi, dan rasa saling percaya.',
                'body' => "Komunitas bukan hanya tentang perangkat lunaknya. Harapan yang jelas, alat moderasi yang berguna, dan diskusi yang saling menghormati membantu semua orang menikmati ruang bersama.",
                'status' => 'published', 'is_featured' => true,
            ],
            [
                'user_id' => $member->id, 'category_id' => $community->id,
                'title' => 'Akhir pekan pertama belajar Laravel',
                'excerpt' => 'Catatan saat mempelajari route, model, migration, dan Blade view.',
                'body' => "Memulai dari siklus request membuat framework lebih mudah dipahami. Route menghubungkan request dengan controller, model merepresentasikan data, dan view membentuk respons.",
                'status' => 'published', 'is_featured' => false,
            ],
            [
                'user_id' => $editor->id, 'category_id' => $tech->id,
                'title' => 'Draf tentang keamanan sejak awal',
                'excerpt' => 'Ini adalah konten contoh yang sengaja belum diterbitkan untuk lab lokal.',
                'body' => 'Konten draf seharusnya tidak muncul pada hasil pencarian publik.',
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

        $firstArticle = Article::where('slug', 'pelajaran-besar-dari-proyek-kecil')->first();
        if ($firstArticle && $firstArticle->comments()->doesntExist()) {
            Comment::create([
                'article_id' => $firstArticle->id,
                'user_id' => $member->id,
                'body' => 'Pengingat yang bagus untuk membuat proyek tetap kecil dan realistis untuk diselesaikan.',
            ]);
            Comment::create([
                'article_id' => $firstArticle->id,
                'user_id' => $editor->id,
                'body' => 'Meninjau hasil sama pentingnya dengan menulis versi pertama.',
            ]);
        }
    }
}
