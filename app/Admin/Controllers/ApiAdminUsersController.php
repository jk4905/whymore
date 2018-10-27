<?php

namespace App\Admin\Controllers;

use App\Models\Category;
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

    public function categories()
    {
        return Category::query()->get(['id', 'name as text']);
    }

    public function downloadRobotConfiguration(RobotConfiguration $robotConfiguration)
    {
        $str = "[配置]" . PHP_EOL;
        $str .= "敏感词={$robotConfiguration->sensitive_words}" . PHP_EOL;
        $str .= "后缀内容={$robotConfiguration->suffix}" . PHP_EOL;
        $str .= "可用群={$robotConfiguration->transmit_group_num}" . PHP_EOL;
        $str .= "未加好友消息={$robotConfiguration->non_friend_msg}" . PHP_EOL;
        $str .= "好友回复={$robotConfiguration->become_friend_msg}" . PHP_EOL;
        $str .= "转发成功回复={$robotConfiguration->transmit_success_msg}" . PHP_EOL;
        $str .= "黑名单={$robotConfiguration->black_list}" . PHP_EOL;
        $str .= "黑名单返回消息={$robotConfiguration->black_list_msg}" . PHP_EOL;
        $str .= "警告拉黑次数={$robotConfiguration->warning_blacklist_count}" . PHP_EOL;
        $str .= "关键词={$robotConfiguration->key_words}" . PHP_EOL;
        $str .= "关键词返回消息={$robotConfiguration->not_key_words_msg}" . PHP_EOL;
        $str .= "字数={$robotConfiguration->transmit_length}" . PHP_EOL;
        $str .= "字数不足返回消息={$robotConfiguration->transmit_length_wrong_msg}" . PHP_EOL;
        $str .= "敏感词返回消息={$robotConfiguration->sensitive_words_msg}" . PHP_EOL;
        $str .= "后缀概率={$robotConfiguration->suffix_odds}" . PHP_EOL;
        $str .= "达标等级={$robotConfiguration->achieve_level}" . PHP_EOL;
        $str .= "不达标回复={$robotConfiguration->not_achieve_level_msg}" . PHP_EOL;
        $str .= "开始时间={$robotConfiguration->invalid_begin_at}" . PHP_EOL;
        $str .= "结束时间={$robotConfiguration->invalid_end_at}" . PHP_EOL;
        $str .= "延迟={$robotConfiguration->transmit_delay}" . PHP_EOL;
        $str .= "管理员={$robotConfiguration->group_admin}" . PHP_EOL;
        $str .= "发布次数={$robotConfiguration->send_count}" . PHP_EOL;
        $str .= "超出回复={$robotConfiguration->overstep_send_count_msg}" . PHP_EOL;
        $str .= "发布一次需积分={$robotConfiguration->send_score}" . PHP_EOL;
        $str .= "充值一元获得积分={$robotConfiguration->recharge_score}" . PHP_EOL;
        $str .= "回复最小积分={$robotConfiguration->reply_min_score}" . PHP_EOL;
        $str .= "回复最大积分={$robotConfiguration->reply_max_score}" . PHP_EOL;
        $str .= "签到最小积分={$robotConfiguration->sign_in_min_score}" . PHP_EOL;
        $str .= "签到最大积分={$robotConfiguration->sign_in_max_score}" . PHP_EOL;
        $str .= "解黑={$robotConfiguration->remove_blank_list_count}" . PHP_EOL;
        $str .= "解黑积分够={$robotConfiguration->remove_blank_list_msg}";

        $path = public_path('upload') . '/配置.ini';
        file_put_contents($path, iconv('UTF-8', 'GB2312//IGNORE', $str));

        $header = [
            'Content-Type' => 'application/txt;charset=ANSI',
        ];
        return response()->download($path, '配置.ini', $header);
    }
}
