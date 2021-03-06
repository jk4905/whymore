<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderItem extends Base
{
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 只取第一个图片
    public function getImageAttribute()
    {
        return collect($this->getImageFullUrl($this->attributes['image']))->first();
    }

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
