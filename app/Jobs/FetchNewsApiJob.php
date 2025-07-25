<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FetchNewsApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $apiKey = config('services.newsapi.key'); // Add this to config/services.php
        $source = Source::where('name', 'NewsAPI')->first();
        $response = Http::get("https://newsapi.org/v2/top-headlines", [
            'country' => 'us',
            'apiKey' => $apiKey,
            'pageSize' => 20,
        ]);

        foreach ($response['articles'] as $data) {
            $published_at = \Carbon\Carbon::parse($data['publishedAt'])->format('Y-m-d H:i:s');
            Article::updateOrCreate([
                'url' => $data['url']
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'author' => $data['author'],
                'urlToImage' => $data['urlToImage'],
                'published_at' => $published_at,
                'source_id' => $source->id,
                'category_id' => Category::inRandomOrder()->first()->id,
            ]);
        }
    }
}
