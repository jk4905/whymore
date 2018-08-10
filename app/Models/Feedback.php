<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Feedback extends Model
{
    protected $guarded = [];

    public function getImageAttribute()
    {
        return $this->getImageFullUrl($this->attributes['image']);
    }

//    public function setImageAttribute($image)
//    {
//        if (is_array($image)) {
//            $image = collect($image);
//            $cdn = 'http://' . env('QINIU_DOMAIN') . '/';
//            $image = $image->map(function ($value) use ($cdn) {
//                return trim($value, $cdn);
//            });
//
//            $this->attributes['image'] = implode(',', $image->toArray());
//        }
//    }

    public function getImageFullUrl($imagesArr)
    {
        $images = explode(',', $imagesArr);
        foreach ($images as $k => $image) {
            if (Str::startsWith($image, ['http://', 'https://']) || empty($image)) {
                continue;
            }
            $disk = Storage::disk('qiniu');
            $images[$k] = $disk->url($image);
        }
        return $images;
    }
}
