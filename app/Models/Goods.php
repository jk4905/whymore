<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Goods extends Model
{
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'id', 'category_id');
    }

    public function getImageAttribute()
    {
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        $disk = Storage::disk('qiniu');
        return $disk->url($this->attributes['image']);
    }
}
