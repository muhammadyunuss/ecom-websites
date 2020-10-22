<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public static function getBanners(){
        //  Get Banner
        $getBanners = Banner::where('status',1)->get()->toArray();
        return $getBanners;
    }
}
