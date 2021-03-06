<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'mobile', 'avatar', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * 优惠券
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')->withPivot('begin_at', 'end_at', 'status')->withTimestamps();
//        return $this->hasManyThrough(Coupon::class, UserCoupon::class,'id','id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getAvatarAttribute()
    {
        if (Str::startsWith($this->attributes['avatar'], ['http://', 'https://']) || empty($this->attributes['avatar'])) {
            return $this->attributes['avatar'];
        }
        $disk = Storage::disk('qiniu');
        return $disk->url($this->attributes['avatar']);
    }
}
