<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Base extends Model
{
    public function getImageAttribute()
    {
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://']) || empty($this->attributes['image'])) {
            return $this->attributes['image'];
        }
        $disk = Storage::disk('qiniu');
        return $disk->url($this->attributes['image']);
    }
}
