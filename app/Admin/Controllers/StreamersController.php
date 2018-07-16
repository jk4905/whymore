<?php

namespace App\Admin\Controllers;

use App\Models\Banner;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class StreamersController extends Controller
{
    use ModelForm;

    public static $status = [1 => '可用', 2 => '不可用'];
    public static $can_redirect = [1 => '跳转', 2 => '不跳转'];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('轮播图列表');
            $content->description('轮播图列表');

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

            $content->header('编辑轮播图');
            $content->description('编辑轮播图');

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

            $content->header('新建轮播图');
            $content->description('新建轮播图');

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
        return Admin::grid(Banner::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->name('名称');

            $grid->image('图片')->image('', 40, 40);

            $grid->redirect_url('跳转链接');

            $grid->sort('排序')->editable()->sortable();

            $grid->can_redirect('是否跳转')->editable('select', self::$status);

            $grid->status('可用')->editable('select', self::$status);

            $grid->filter(function ($filter) {

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 在这里添加字段过滤器
                $filter->like('name', '商品名');

                $filter->equal('can_redirect')->select(self::$can_redirect);

                $filter->equal('status')->select(self::$status);
            });

            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Banner::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('name', '名称');

            $form->image('image', '图片')->uniqueName();

            $form->url('redirect_url', '跳转链接');

            $form->select('can_redirect', '是否跳转')->options(self::$can_redirect);

            $form->number('sort', '排序');


            $form->select('status', '可用')->options(self::$status);

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '结束时间');
        });
    }
}
