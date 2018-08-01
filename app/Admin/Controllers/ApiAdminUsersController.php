<?php

namespace App\Admin\Controllers;

use App\Models\RobotConfiguration;
use Encore\Admin\Auth\Database\Administrator;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;

class ApiAdminUsersController extends Controller
{
    public function admins(Request $request)
    {
        $q = $request->get('q');

        return Administrator::query()->where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    public function downloadRobotConfiguration(RobotConfiguration $robotConfiguration)
    {
        $str = "[配置]\r\n";
        $str .= "敏感词={$robotConfiguration->sensitive_words}\r\n";
        $str .= "后缀内容={$robotConfiguration->suffix}\r\n";
        $str .= "可用群={$robotConfiguration->transmit_group_num}\r\n";
        $str .= "未加好友消息={$robotConfiguration->non_friend_msg}\r\n";
        $str .= "好友回复={$robotConfiguration->become_friend_msg}\r\n";
        $str .= "转发成功回复={$robotConfiguration->transmit_success_msg}\r\n";
        $str .= "黑名单={$robotConfiguration->black_list}\r\n";
        $str .= "黑名单返回消息={$robotConfiguration->black_list_msg}\r\n";
        $str .= "警告拉黑次数={$robotConfiguration->warning_blacklist_count}\r\n";
        $str .= "关键词={$robotConfiguration->key_words}\r\n";
        $str .= "关键词返回消息={$robotConfiguration->not_key_words_msg}\r\n";
        $str .= "字数={$robotConfiguration->transmit_length}\r\n";
        $str .= "字数不足返回消息={$robotConfiguration->transmit_length_wrong_msg}\r\n";
        $str .= "敏感词返回消息={$robotConfiguration->sensitive_words_msg}\r\n";
        $str .= "后缀概率={$robotConfiguration->suffix_odds}\r\n";
        $str .= "达标等级={$robotConfiguration->achieve_level}\r\n";
        $str .= "不达标回复={$robotConfiguration->not_achieve_level_msg}\r\n";
        $str .= "开始时间={$robotConfiguration->invalid_begin_at}\r\n";
        $str .= "结束时间={$robotConfiguration->invalid_end_at}\r\n";
        $str .= "延迟={$robotConfiguration->transmit_delay}\r\n";
        $str .= "管理员={$robotConfiguration->group_admin}\r\n";
        $str .= "发布次数={$robotConfiguration->send_count}\r\n";
        $str .= "超出回复={$robotConfiguration->overstep_send_count_msg}\r\n";
        $str .= "发布一次需积分={$robotConfiguration->send_score}\r\n";
        $str .= "充值一元获得积分={$robotConfiguration->recharge_score}\r\n";
        $str .= "回复最小积分={$robotConfiguration->reply_min_score}\r\n";
        $str .= "回复最大积分={$robotConfiguration->reply_max_score}\r\n";
        $str .= "签到最小积分={$robotConfiguration->sign_in_min_score}\r\n";
        $str .= "签到最大积分={$robotConfiguration->sign_in_max_score}\r\n";
        $str .= "解黑={$robotConfiguration->remove_blank_list_count}\r\n";
        $str .= "解黑积分够={$robotConfiguration->remove_blank_list_msg}";

        $path = public_path('upload') . '/配置.ini';
        file_put_contents($path, $str);
        return response()->download($path);
    }
}
