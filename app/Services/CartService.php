<?php

namespace App\Services;

use App\Exceptions\InvalidRequestException;
use App\Models\Goods;
use function Couchbase\defaultDecoder;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CartService
{
    protected static $cart = 'shopping_cart';
    protected $goodsModel;

    public function __construct()
    {
        $this->cartInstance = Cart::instance(self::$cart);
        $this->disk = Storage::disk('qiniu');
        $this->goodsModel = new Goods();
    }

    public function getContent()
    {
        $user = Auth::user();
        if (empty($user->id)) {
            $user = Auth::guard('api')->user();
        }
        if (empty($user->id)) {
            return [];
        }
        try {
            $this->cartInstance->restore($user->id);
            $this->cartInstance->store($user->id);
        } catch (\Exception $e) {

        }
        return $this->cartInstance->content();
    }

    /**
     * 购物车字典
     *
     * @return mixed
     */
    public function getCartDict()
    {
        $user = Auth::user();
        if (empty($user->id)) {
            $user = Auth::guard('api')->user();
        }
        if (empty($user->id)) {
            return [];
        }
        $cart = $this->getContent();
        if (empty($cart)) {
            return [];
        }
        $cartDict = $cart->mapWithKeys(function ($item) {
            return [$item->id => [
                'goods_id' => $item->id,
                'row_id' => $item->rowId,
                'qty' => $item->qty,
                'name' => $item->name
            ]];
        });
        return $cartDict->toArray();
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

    /**
     * 搜索购物车
     *
     * @param $value
     * @return mixed
     * @throws InvalidRequestException
     */
    public function search($value, $column)
    {
        $user = Auth::user();
        $this->cartInstance->restore($user->id);
        $this->cartInstance->store($user->id);
        try {
            return $this->cartInstance->content()->where($column, $value)->first();
        } catch (\Exception $e) {
            throw new InvalidRequestException(40008);
        }
    }

    /**
     * 更新购物车商品
     *
     * @param $row
     * @throws InvalidRequestException
     */
    public function update($row)
    {
        $cartGoods = $this->search($row['row_id'], 'rowId');
        $goods = $this->goodsModel->getGoods($cartGoods->id)->toArray();
        $goods['qty'] = $row['qty'];
        $user = Auth::user();
        $this->cartInstance->restore($user->id);
        $this->cartInstance->update($row['row_id'], $goods);
        $this->cartInstance->store($user->id);
        return true;
    }


    /**
     * 得到购物车中的数量和购物车商品 id
     *
     * @param $list
     * @return mixed
     */
    public function getQtyAndRowId($list)
    {
        $cartDict = $this->getCartDict();
        if (empty($cartDict)) {
            return $list;
        }
        $newList = $list->each(function ($item) use ($cartDict) {
            $item->row_id = empty($cartDict[$item->id]) ? 0 : $cartDict[$item->id]['row_id'];
            $item->qty = empty($cartDict[$item->id]) ? 0 : $cartDict[$item->id]['qty'];
            return $item;
        });

        return $newList;
    }
}
