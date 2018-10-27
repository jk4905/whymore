<?php

namespace App\Models;

use App\Services\CartService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Goods extends Base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function category()
    {
//        return $this->belongsTo('App\Models\Category', 'id', 'category_id');
        return $this->belongsTo('App\Models\Category',  'category_id','id');
    }

    /**
     * 查询商品
     *
     * @param $goodsId
     * @return mixed
     */
    public function getGoods($goodsId)
    {
        return $this->newQuery()->whereStatus(1)->findOrFail($goodsId);
    }

    public function getSalePriceAttribute()
    {
        return number_format($this->attributes['sale_price'], 2, '.', '');
    }

    public function getImageAttribute()
    {
        return $this->getImageFullUrl($this->attributes['image']);
    }

    public function setImageAttribute($image)
    {
        if (is_array($image)) {
            $image = collect($image);
            $cdn = 'http://' . env('QINIU_DOMAIN') . '/';
            $image = $image->map(function ($value) use ($cdn) {
                return trim($value, $cdn);
            });

            $this->attributes['image'] = implode(',', $image->toArray());
        }
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
