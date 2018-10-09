<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RobotMessageUser extends Model
{
    protected $fillable = [
        'qq', 'age', 'user_name', 'avatar', 'sex'
    ];
}
