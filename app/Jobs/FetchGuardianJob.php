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
use Carbon\Carbon;

class FetchGuardianJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $apiKey = config('services.guardian.key');
        $source = Source::firstOrCreate(['name' => 'The Guardian']);
        
        if (!$source) {
            logger()->error('Guardian source not found or could not be created.');
            return;
        }

        $response = Http::get('https://content.guardianapis.com/search', [
            'api-key' => $apiKey,
            'show-fields' => 'thumbnail,byline,trailText',
            'page-size' => 20,
        ]);

        
        
        if ($response->successful()) {
            foreach ($response['response']['results'] as $item) {
                $published_at = \Carbon\Carbon::parse($item['webPublicationDate'])->format('Y-m-d H:i:s');
                if(!$item['webTitle']){
                    Article::updateOrCreate([
                        'url' => $item['webUrl'],
                    ], [
                        'title' => $item['webTitle'],
                        'description' => $item['fields']['trailText'] ?? null,
                        'author' => $item['fields']['byline'] ?? null,
                        'urlToImage' => $item['fields']['thumbnail'] ?? null,
                        'published_at' => $published_at,
                        'source_id' => $source->id,
                        'category_id' => Category::inRandomOrder()->first()->id,
                    ]);
                    
                }
            }    
        }
    }
}
