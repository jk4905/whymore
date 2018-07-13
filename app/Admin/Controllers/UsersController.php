<?php

namespace App\Admin\Controllers;

use App\Models\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use function foo\func;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    use ModelForm;
    public $disk;

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

            $content->header('用户列表');
            $content->description('用户列表');

            $content->body($this->grid());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(User::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->name('用户名');

            $grid->mobile('手机号');

            $disk = $this->disk;

            $grid->avatar('头像')->display(function ($value) use ($disk) {
                return $value ? $disk->url($value) : '';
            })->image('', 40, 40);

            $grid->created_at('注册时间');

            // 不在页面显示 `新建` 按钮
            $grid->disableCreateButton();

            $grid->actions(function ($actions) {
                // 不在每一行后面展示删除按钮
                $actions->disableDelete();

                // 不在每一行后面展示编辑按钮
                $actions->disableEdit();
            });

            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

}
