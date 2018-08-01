<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class RobotConfiguration extends Model
{
    public function admin_user()
    {
        return $this->hasOne(Administrator::class, 'id', 'admin_id');
    }
}
