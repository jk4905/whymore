<?php

namespace App\Admin\Controllers;

use App\Models\Coupon;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CouponsController extends Controller
{
    use ModelForm;

    public static $status = [1 => '可用', 2 => '禁用'];
    public static $type = [1 => '满减', 2 => '打折'];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('优惠券');
            $content->description('列表');

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

            $content->header('优惠券');
            $content->description('编辑');

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

            $content->header('优惠券');
            $content->description('新增');

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
        return Admin::grid(Coupon::class, function (Grid $grid) {

//            禁用行选择checkbox
            $grid->disableRowSelector();

            $grid->id('ID')->sortable();

            $grid->name('券名');

            $grid->condition('条件')->sortable();

            $grid->discount('折扣')->sortable();

            $grid->type('类型')->display(function ($value) {
                return self::$type[$value];
            });

            $grid->expires('有效期');

            $grid->description('描述');

            $grid->status('状态')->editable('select', self::$status);

            $grid->created_at('创建时间')->sortable();
            $grid->updated_at('修改时间')->sortable();

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });

            $grid->filter(function ($filter) {

                // 在这里添加字段过滤器
                $filter->like('name', '优惠券名');

                $filter->equal('status','状态')->select(self::$status);

                $filter->equal('type','类型')->select(self::$type);
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
        return Admin::form(Coupon::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('name', '券名')->rules('required|max:100')->placeholder('请输入券名');;

            $form->text('description', '描述')->placeholder('请输入描述');

            $form->number('condition', '条件')->rules('required|numeric|min:0')->help('满多少金额');

            $form->number('discount', '折扣')->rules('required|numeric|min:0')->help('满减请输入任意正数，折扣请输入0-1的小数');

            $form->number('expires', '有效期（天）')->rules('required|integer|min:0')->help('请输入任意正整数');

            $form->radio('type', '类型')->options(self::$type)->default(1)->rules('required');

            $form->radio('status', '状态')->options(self::$status)->default(1)->rules('required');

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }
}
