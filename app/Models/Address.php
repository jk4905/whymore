<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //
    protected $guarded = [];

    public function getFullAddress()
    {
        return $this->province_name . $this->city_name . $this->area_name . $this->detailed_address;
    }
}
