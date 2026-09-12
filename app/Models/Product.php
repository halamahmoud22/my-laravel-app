<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
        'image',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path('assets/media/products/' . $this->image))) {
            return asset('assets/media/products/' . $this->image);
        }

        if ($this->image && (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://'))) {
            return $this->image;
        }

        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        if (file_exists(public_path('assets/media/products/' . $this->id . '.png'))) {
            return asset('assets/media/products/' . $this->id . '.png');
        }

        return asset('assets/media/products/1.png');
    }
}
