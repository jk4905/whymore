<?php

namespace App\Libraries;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Carts
{
    protected static $cart = 'shopping_cart';

    public function __construct()
    {
        $this->cartInstance = Cart::instance(self::$cart);
        $this->disk = Storage::disk('qiniu');
    }

    public function getContent()
    {
        $user = Auth::user();
        $this->cartInstance->restore($user->id);
        $this->cartInstance->store($user->id);
        return $this->cartInstance->content();
    }

    /**
     * 通过购物车获取商品列表
     * @param $content
     * @return array
     */
    public function getGoodsList($content)
    {
        $goodsList = [];
        foreach ($content as $key => $row) {
            $goods = $row->model->toArray();
            if ($goods['status'] == 2) {
                unset($content[$key]);
                continue;
            }
            $goods['image'] = $this->disk->getUrl($goods['image']);
            $goods['row_id'] = $row->rowId;
            $goods['qty'] = $row->qty;
            $goodsList[] = $goods;
        }
        return $goodsList;
    }

    /**
     * 删除购物车中商品
     *
     * @param $rowIds
     * @return bool
     */
    public function remove($rowIds)
    {
        $rows = collect($rowIds);
        $user = Auth::user();
        $this->cartInstance->restore($user->id);
        $rows->each(function ($item, $key) {
            if ($this->cartInstance->get($item)) {
                $this->cartInstance->remove($item);
            }
        });
        $this->cartInstance->store($user->id);
        return true;
    }
}
