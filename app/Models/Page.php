<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasSeoFields;

    protected $fillable = ['title', 'slug', 'content', 'meta_title', 'meta_description', 'status'];

    public function getSlugSource(): string
    {
        return 'title';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
