<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category(){
        return $this->belongsTo('App\Category','category_id');
    }

    public function section(){
        return $this->belongsTo('App\Section','section_id');
    }

    public function brand(){
        return $this->belongsTo('App\Brand','brand_id');
    }
    public function attributes(){
        return $this->hasMany('App\ProductsAttribute');
    }

    public function images(){
        return$this->hasMany('App\ProductsImage');
    }

    public static function productFilters(){
        $productFilters['fabricArray'] = array('Cotton','Polyester','Wool');
        $productFilters['sleeveArray'] = array('Full Sleeve','Half Sleeve','Short Sleeve','Sleeveless');
        $productFilters['patternArray'] = array('Checked','Plain','Printed','Self','Solid');
        $productFilters['fitArray'] = array('Regular','Slim');
        $productFilters['occasionArray'] = array('Casual','Formal');
        return $productFilters;
    }

    public static function getDiscountedPrice($product_id){
        $proDetails = Product::select('product_price','product_discount','category_id')->where('id',$product_id)->first()->toArray();
        // echo "<pre>"; print_r($proDetails); die;
        $catDetails = Category::select('category_discount')->where('id', $proDetails['category_id'])->first()->toArray();
        if($proDetails['product_discount']>0){
            //  if Product Dicount is not added and category discount adde from admin panel
            $discounted_price = $proDetails['product_price'] - ($proDetails['product_price']*$proDetails['product_discount']/100);
            // ? Sale Price = Cost Price - Discount Price
            // 450      =       500 - (500*10/100 = 50 )
        }else if($catDetails['category_discount']>0){
            //  if Product discount is not added and category idscount added from admin panel
            $discounted_price = $proDetails['product_price'] - ($proDetails['product_price']*$catDetails['category_discount']/100);
        }else{
            $discounted_price = 0;
        }
        return $discounted_price;
    }

    public static function getDiscountedAttrPrice($product_id, $size){
        $proAttrPrice = ProductsAttribute::where(['product_id' => $product_id, 'size' => $size])->first()->toArray();
        $proDetails = Product::select('product_discount','category_id')->where('id',$product_id)->first()->toArray();
        $catDetails = Category::select('category_discount')->where('id',$proDetails['category_id'])->first()->toArray();

        if($proDetails['product_discount']>0){
            //  if Product Dicount is not added and category discount adde from admin panel
            $final_price = $proAttrPrice['price'] - ($proAttrPrice['price']*$proDetails['product_discount']/100);
            $discount = $proAttrPrice['price'] - $final_price;
            // ? Sale Price = Cost Price - Discount Price
            // 450      =       500 - (500*10/100 = 50 )
        }else if($catDetails['category_discount']>0){
            //  if Product discount is not added and category idscount added from admin panel
            $final_price = $proAttrPrice['price'] - ($proAttrPrice['price']*$catDetails['category_discount']/100);
            $discount = $proAttrPrice['price'] - $final_price;
        }else{
            $final_price = $proAttrPrice['price'];
            $discount = 0;
        }
        return array('product_price' => $proAttrPrice['price'], 'final_price'=>$final_price, 'discount'=>$discount);
    }
}
