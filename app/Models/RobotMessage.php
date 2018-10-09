<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RobotMessage extends Model
{
    const TYPE_USER       = 1;
    const TYPE_BACKGROUND = 2;

    protected $fillable = [
        'from', 'to', 'send_time', 'content', 'images', 'type'
    ];
}
