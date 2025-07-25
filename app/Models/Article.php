<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'author',
        'url',
        'urlToImage',
        'published_at',
        'source_id',
        'category_id',
    ];

    public function source() {
        return $this->belongsTo(Source::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
