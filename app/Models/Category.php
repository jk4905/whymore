<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function parentCategory()
    {
        return $this->belongsTo('App\Models\Category', 'id', 'pid');
    }

    public function childrenCategories()
    {
//        第一个参数是要关联的 Category2 的模型名称
//        第二个参数是外键，是关联的 Category 的键。也就是 Category1 的 id 关联上 Category2 的 pid。
//        第三个参数是 Category1 的主键
        return $this->hasMany('App\Models\Category', 'pid', 'id');
    }

    public function allChildrenCategories()
    {
        return $this->childrenCategories()->with('goods');
    }

    /**
     * 一对多
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsback()
    {
        return $this->hasMany('App\Models\Goods', 'category_id', 'id');
    }

    /**
     * 多对多
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function goods()
    {
//        第一个参数，关联的类名
//        第二个参数，关联中间表名
//        第三个参数，本模型在中间表的id,
//        第四个参数，关联模型在中间表的id,
//        return $this->belongsToMany(Goods::class,'category_goods','category_id','goods_id');
        return $this->belongsToMany(Goods::class);
    }
}
