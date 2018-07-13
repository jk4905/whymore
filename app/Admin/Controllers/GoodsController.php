<?php

namespace App\Admin\Controllers;

use App\Models\Goods;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Storage;

class GoodsController extends Controller
{
    use ModelForm;

    public $disk;

    public static $status = [1 => '上架', 2 => '下架'];

    public function __construct()
    {
        parent::__construct();
        $this->disk = Storage::disk('qiniu');
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('商品列表');
            $content->description('商品列表');

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

            $content->header('编辑商品');
            $content->description('编辑商品');

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

            $content->header('新建商品');
            $content->description('新建商品');

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

            $grid->description('描述');

            $disk = $this->disk;

            $grid->image('图片')->display(function ($value) use ($disk) {
                return ($value ? $disk->url($value) : '');
            })->image('', 40, 40);

            $grid->sale_price('售价')->editable()->sortable();

            $grid->sales('销售量')->editable()->sortable();

            $grid->keyword('关键字')->editable();

            $grid->status('状态')->editable('select', self::$status);

            $grid->filter(function ($filter) {

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 在这里添加字段过滤器
                $filter->like('name', '商品名');

                $filter->equal('status')->select(self::$status);
            });

            $grid->created_at('创建时间')->sortable();
            $grid->updated_at('修改时间')->sortable();
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

            $form->text('name', '商品名');

            $form->text('description', '描述');

            $form->image('image', '图片')->uniqueName();

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
