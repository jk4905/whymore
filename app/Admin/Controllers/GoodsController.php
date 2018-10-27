<?php

namespace App\Admin\Controllers;

use App\Models\Goods;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class GoodsController extends Controller
{
    use ModelForm;

    public static $status = [1 => '上架', 2 => '下架'];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('商品');
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

            $content->header('商品');
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

            $content->header('商品');
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
        return Admin::grid(Goods::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->name('商品名');

            $grid->column('category.name','分类');

            $grid->description('描述');

            $grid->image('图片')->image('', 40, 40);

            $grid->sale_price('售价')->editable()->sortable();

            $grid->sales('销售量')->editable()->sortable();

            $grid->sort('排序')->editable()->sortable();

            $grid->keyword('关键字')->editable();

            $grid->status('状态')->editable('select', self::$status);

            $grid->created_at('创建时间')->sortable();
            $grid->updated_at('修改时间')->sortable();

            $grid->filter(function ($filter) {

                // 在这里添加字段过滤器
                $filter->like('name', '商品名');

                $filter->equal('status', '状态')->select(self::$status);
            });

            $grid->filter(function ($filter) {

                // 在这里添加字段过滤器
                $filter->like('name', '商品名');

                $filter->between('sales', '销售量');

                $filter->between('sale_price', '售价');

                $filter->keyword('keyword', '关键字');

                $filter->equal('status', '状态')->select(self::$status);

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
        return Admin::form(Goods::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->select('category_id', '商品分类')->options('/admin/api/categories');

            $form->text('name', '商品名');

            $form->text('description', '描述');

            $form->multipleImage('image', '图片')->removable();

            $form->number('sale_price', '售价');

            $form->number('sales', '销售量');

            $form->number('sort', '排序');

            $form->text('keyword', '关键字');

            $form->select('status', '状态')->options(self::$status);

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '结束时间');
        });
    }
}
