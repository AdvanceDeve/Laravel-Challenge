<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;

class ArticleFetcherService
{
    public function saveOrUpdate(array $data, string $sourceName)
    {
        $source = Source::firstOrCreate(['name' => $sourceName]);
        $category = Category::inRandomOrder()->first();

        return Article::updateOrCreate([
            'url' => $data['url'],
        ], [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'author' => $data['author'] ?? null,
            'urlToImage' => $data['urlToImage'] ?? null,
            'published_at' => $data['published_at'],
            'source_id' => $source->id,
            'category_id' => $category?->id,
        ]);
    }
}
