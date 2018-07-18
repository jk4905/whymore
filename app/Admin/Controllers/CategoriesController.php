<?php

namespace App\Admin\Controllers;

use App\Models\Category;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;

class CategoriesController extends Controller
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

            $content->header('商品分类');
            $content->description('商品分类');

//            $content->body();
            $content->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('categories'));

                    $form->text('name', '类型名称')->rules(['required']);

                    $form->image('image', '图片')->uniqueName();

                    $form->number('sort', '排序序号')->default(0)->rules(['numeric']);

                    $form->select('pid', '父类名称')->options(Category::selectOptions());

                    $form->hidden('_token')->default(csrf_token());

                    $column->append((new Box(trans('category.new'), $form))->style('success'));
                });
            });
        });
    }

    public function treeView()
    {
        return Category::tree(function (Tree $tree) {
            $tree->query(function ($model) {
                return $model->where('status', 1)->orderByDesc('sort');
            });
            $tree->branch(function ($branch) {
                $payload = "<img class='img' src='{$branch['image']}' style='width: 30px;'><strong>{$branch['name']}</strong>";
                return $payload;
            });
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

            $content->header('修改商品分类');
            $content->description('修改商品分类');

            $content->body($this->editForm()->edit($id));
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

            $content->header('添加商品分类');
            $content->description('添加商品分类');
            // 添加text类型的input框
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
        return Admin::grid(Category::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Category::class, function (Form $form) {

            $form->display('id', 'ID');
            // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
            $form->text('name', '分类名称')->rules('required');
            $form->number('sort', '排序')->rules('required');
            $form->image('image', '图片')->uniqueName();
            $form->number('pid', '父级');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    protected function editForm()
    {
        return Admin::form(Category::class, function (Form $form) {
            $form->display('id', 'ID');
            // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
            $form->text('name', '分类名称')->rules('required');
            $form->image('image', '图片')->uniqueName();
        });
    }


}
