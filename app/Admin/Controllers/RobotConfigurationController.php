<?php

namespace App\Admin\Controllers;

use App\Models\RobotConfiguration;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class RobotConfigurationController extends Controller
{
    use ModelForm;

    const HEADER = '机器人设置';

    public static $status = ['1' => '启用', '2' => '禁止'];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header(self::HEADER);
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
        if (Admin::user()->id != 1 && !Admin::user()->can('robot_configuration_manage')) {
            $conf = RobotConfiguration::query()->where('id', $id)->first();
            if ($conf['admin_id'] != Admin::user()->id) {
                return response()->redirectTo('/admin/robot_configuration');
            }
        }

        return Admin::content(function (Content $content) use ($id) {

            $content->header(self::HEADER);
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

            $content->header(self::HEADER);
            $content->description('新建');

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
        return Admin::grid(RobotConfiguration::class, function (Grid $grid) {
//            禁用行选择checkbox
            $grid->disableRowSelector();
            if (!Admin::user()->can('robot_configuration_manage')) {
//            禁用创建按钮
                $grid->disableCreateButton();
//                非管理员才限制
                $grid->model()->where('admin_id', '=', Admin::user()->id)->where('status', 1);
            }

//            $grid->id('ID')->sortable();

            $grid->robot_num('机器人号');
//            $grid->admin_id('管理员号');
            $grid->column('admin_user.name', '管理员');
            if (Admin::user()->can('robot_configuration_manage')) {
                $grid->status('状态')->editable('select', self::$status);
                $grid->remark('备注');
            } else {
                $grid->status('状态')->display(function ($value) {
                    return self::$status[$value];
                });
            }


            $grid->actions(function ($actions) {
                $actions->disableDelete();
//                $actions->disableEdit();

                if (Admin::user()->can('robot_configuration_manage')) {
                    $url = '/admin/api/robot_conf/' . $this->row->id;
                    // append一个操作
                    $actions->append('<a href="' . $url . '" target="_blank"><i class="fa fa-download"></i></a>');
                }

            });
//            $grid->created_at('创建时间');
//            $grid->updated_at('修改时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(RobotConfiguration::class, function (Form $form) {
//            $form->display('id', 'ID');
            if (Admin::user()->can('robot_configuration_manage')) {
                $form->text('robot_num', '机器人号')->rules('required|integer');
                $form->select('admin_id', '管理员ID')->options(function ($id) {
                    $user = Administrator::find($id);

                    if ($user) {
                        return [$user->id => $user->name];
                    } else {
                        return [];
                    }
                })->ajax('/admin/api/admins');
            }


            $form->text('sensitive_words', '敏感词')->rules('nullable|string');
            $form->text('suffix', '后缀')->rules('nullable|string');


            $form->text('transmit_group_num', '转发群号')->rules(['nullable', 'regex:/^\d+(\+\d+)*$/']);
            $form->text('non_friend_msg', '非好友回复')->rules('nullable|string');
            $form->text('become_friend_msg', '成为好友回复')->rules('nullable|string');
            $form->text('transmit_success_msg', '转发成功回复')->rules('nullable|srting');


            $form->text('black_list', '黑名单')->rules(['nullable', 'regex:/^\d+(\+\d+)*$/']);
            $form->text('black_list_msg', '黑名单回复')->rules('nullable|string');
            $form->number('warning_blacklist_count', '拉黑警告次数')->rules('integer|min:0');

            $form->text('key_words', '转发关键字')->rules('nullable|string');
            $form->text('not_key_words_msg', '未有关键字回复')->rules('nullable|string');

            if (Admin::user()->can('robot_configuration_manage')) {
                $form->number('transmit_length', '转发内容长度')->rules('integer|min:0');
                $form->text('transmit_length_wrong_msg', '转发内容长度不达标回复')->rules('nullable|string');
            } else {
                $form->display('transmit_length', '转发内容长度');
                $form->display('transmit_length_wrong_msg', '转发内容长度不达标回复');
            }

            $form->text('sensitive_words_msg', '包含敏感词回复')->rules('nullable|string');
            $form->number('suffix_odds', '后缀出现几率')->rules('integer|min:0');

            if (Admin::user()->can('robot_configuration_manage')) {
                $form->number('achieve_level', '达标等级')->rules('integer|min:0');
                $form->text('not_achieve_level_msg', '不达标等级回复')->rules('nullable|string');
                $form->number('invalid_begin_at', '机器人无效开始时间')->rules('integer|min:0');
                $form->number('invalid_end_at', '机器人无效结束时间')->rules('integer|min:0');
                $form->number('transmit_delay', '转发延迟时间（单位：ms）')->rules('integer|min:0');
            } else {
                $form->display('achieve_level', '达标等级');
                $form->display('not_achieve_level_msg', '不达标等级回复');
                $form->display('invalid_begin_at', '机器人无效开始时间');
                $form->display('invalid_end_at', '机器人无效结束时间');
                $form->display('transmit_delay', '转发延迟时间（单位：ms）');
            }

            $form->text('group_admin', '管理员QQ')->rules(['nullable', 'regex:/^\d+(\+\d+)*$/']);
            $form->number('send_count', '消息发布限制次数')->rules('integer|min:0');
            $form->text('overstep_send_count_msg', '超出次数回复')->rules('nullable|string');


            $form->number('send_score', '发布一次所需积分')->rules('integer|min:0');
            if (Admin::user()->can('robot_configuration_manage')) {
                $form->number('recharge_score', '充值一元获得积分')->rules('integer|min:0');
            } else {
                $form->display('recharge_score', '充值一元获得积分');
            }
            $form->number('reply_min_score', '回复最小积分')->rules('integer|min:0');
            $form->number('reply_max_score', '回复最大积分')->rules('integer|min:0');
            $form->number('sign_in_min_score', '签到最大积分')->rules('integer|min:0');
            $form->number('sign_in_max_score', '签到最大积分')->rules('integer|min:0');

            if (Admin::user()->can('robot_configuration_manage')) {
                $form->number('remove_blank_list_count', '解除黑名单所需积分')->rules('integer|min:0');
                $form->text('remove_blank_list_msg', '解除黑名单成功回复')->rules('nullable|string');

//            $form->textarea('menu', '菜单')->rules('');

                $form->radio('status', '状态')->options(self::$status)->rules('integer');
                $form->text('remark', '备注')->rules('nullable|string');
            } else {
                $form->display('remove_blank_list_count', '解除黑名单所需积分');
                $form->display('remove_blank_list_msg', '解除黑名单成功回复');
            }

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }
}
