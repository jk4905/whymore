<?php

namespace App\Admin\Controllers;

use App\Models\Order;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use function foo\func;

class OrdersController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('订单列表');
            $content->description('订单列表');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Order::class, function (Grid $grid) {

//            禁用创建按钮
            $grid->disableCreateButton();
//            禁用行选择checkbox
            $grid->disableRowSelector();

            $grid->model()->with('items')->orderBy('id', 'DESC');

            $grid->model()->with('coupon');


            $grid->order_id('订单号')->sortable();;

//            $grid->real_amount('实际支付')->sortable();

//            $grid->user_id('用户id');

//            $grid->payment_type('支付类型');

//            $grid->payment_no('支付单号');

//            $grid->paid_at('支付时间')->sortable();
            $grid->column('支付')->display(function () {
                $paymentType = Order::$paymentType[$this->payment_type];
                $paymentNo = $this->payment_no ?: '';
                $html = '<ul style="padding-left: 0px">';
                $html .= "<li style='list-style-type:none'>支付类型：$paymentType</li>";
                $html .= "<li style='list-style-type:none'>支付单号：$paymentNo</li>";
                $html .= "<li style='list-style-type:none;'>支付时间：$this->paid_at</li>";
                $html .= "<li style='list-style-type:none;'>备注：$this->remark</li>";
                $html .= '</ul>';
                return $html;
            });

//            $grid->coupon_id('优惠券id');

            $grid->column('金额')->display(function () {
                $html = '<ul style="padding-left: 0px">';
                $html .= "<li style='list-style-type:none'>订单金额：$this->total_amount</li>";
                $html .= "<li style='list-style-type:none;'>运费金额：$this->freight</li>";
                $html .= "<li style='list-style-type:none;color: red;'>折扣金额：-$this->discount</li>";
                $html .= "<li style='list-style-type:none;color: #2795e9'>实际支付：$this->real_amount</li>";
                $html .= '</ul>';
                return $html;
            });

            $grid->column('用户信息')->display(function () {
                $html = '<ul style="padding-left: 0px">';
                $html .= "<li style='list-style-type:none'>$this->user_id</li>";
                $html .= "<li style='list-style-type:none'>$this->name</li>";
                $html .= "<li style='list-style-type:none'>$this->mobile</li>";
                $html .= "<li style='list-style-type:none'>$this->address</li>";
                $html .= '</ul>';
                return $html;
            });


            $grid->column('配送')->display(function () {
                $shippingType = Order::$shippingType[$this->shipping_type];
                $html = '<ul style="padding-left: 0px">';
                $html .= "<li style='list-style-type:none'>配送方式：$shippingType</li>";
                $html .= "<li style='list-style-type:none'>快递名称：$this->ship_name</li>";
                $html .= "<li style='list-style-type:none'>快递单号：$this->ship_no</li>";
                $html .= "<li style='list-style-type:none'>发货时间：$this->shipped_at</li>";
                $html .= '</ul>';
                return $html;
            });

//            $grid->remark('备注');

            $grid->status('状态')->display(function ($value) {
                return Order::$status[$value];
            });

//            $grid->created_at('创建时间')->sortable();
//            $grid->updated_at('修改时间')->sortable();

            $grid->column('详情')->expand(function () {
                $profile = [
                    '<p class="label label-success">优惠券名字</p>' => '<strong>' . $this->coupon['name'] . '</strong>',
                    '<p class="label label-success">优惠券描述</p>' => '<strong>' . $this->coupon['description'] . '</strong>',
//                    '<p class="label label-info">地址</p>' => '<strong>' . $this->address . '</strong>',
                ];

                foreach ($this->items as $k => $item) {
                    $key = '<p class="label label-info">商品' . ($k + 1) . '</p>';
                    $profile[$key] = '<strong>' . $item['goods_name'] . '*<span style="color: red">' . $item['qty'] . '</span></strong>';
                }
                return new Table([], $profile);
            }, '详情', 'user_amount');

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Order::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '结束时间');
        });
    }
}
