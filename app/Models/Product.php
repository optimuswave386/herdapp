<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'stock_quantity',
        'category',
        'status',
    ];

    protected $hidden = [
        //
        'status',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /** Path on the public disk if the image was uploaded here (not an external URL), else null. */
    public function storedImagePath(): ?string
    {
        return $this->image_url && str_starts_with($this->image_url, '/storage/')
            ? substr($this->image_url, strlen('/storage/'))
            : null;
    }


}
