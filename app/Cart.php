<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Session;

class Cart extends Model
{
    use HasFactory;

    public static function userCartItems(){
        if(Auth::check()){
            $userCardItems = Cart::with(['product'=>function($query){$query->select('id','product_name','category_id','product_code','product_weight','product_color','main_image');}])->where('user_id',Auth::user()->id)->orderBy('id','Desc')->get()->toArray();
        }else{
            $userCardItems = Cart::with(['product'=>function($query){$query->select('id','product_name','category_id','product_code','product_weight','product_color','main_image');}])->where('session_id',Session::get('session_id'))->orderBy('id','Desc')->get()->toArray();
        }
        return $userCardItems;
    }

    public function product(){
        return $this->belongsTo('App\Product','product_id');
    }


    public static function getProductAttrPrice($product_id, $size){
        $attrPrice = ProductsAttribute::select('price')->where(['product_id' => $product_id, 'size' => $size])->first()->toArray();
        return $attrPrice['price'];
    }
}
