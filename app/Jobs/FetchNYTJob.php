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

class FetchNYTJob implements ShouldQueue
{
    

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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $apiKey = config('services.nyt.key');
        // dd($apiKey); 
        $source = Source::firstOrCreate(['name' => 'New York Times']);
        
        $response = Http::get('https://api.nytimes.com/svc/topstories/v2/home.json', [
            'api-key' => $apiKey,
        ]);
        
        if ($response->successful()) {
            foreach ($response['results'] as $item) {
                Article::updateOrCreate([
                    'url' => $item['url'],
                ], [
                    'title' => $item['title'],
                    'description' => $item['abstract'],
                    'author' => $item['byline'] ?? null,
                    'urlToImage' => $item['multimedia'][0]['url'] ?? null,
                    'published_at' => $item['published_date'],
                    'source_id' => $source->id,
                    'category_id' => Category::inRandomOrder()->first()->id,
                ]);
            }
        }
    }
}
