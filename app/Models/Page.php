<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasSeoFields;

    protected $fillable = ['title', 'slug', 'content', 'meta_title', 'meta_description', 'status'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('published_pages_shared'));
        static::deleted(fn () => Cache::forget('published_pages_shared'));
    }

    public function getSlugSource(): string
    {
        return 'title';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
