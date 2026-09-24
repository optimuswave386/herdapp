<?php

namespace App\Models;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Posts extends Model implements HasMedia
{
    
    use HasFactory;
    use InteractsWithMedia;

    // Optional: Define specific media collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
        //$this->addMediaCollection('downloads')->disk('s3'); // Example of using a different disk
    }
    
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(200)
             ->height(200);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'author',
        'excerpt',
        'image',
        'slug',
        'category_id',
        'user_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
