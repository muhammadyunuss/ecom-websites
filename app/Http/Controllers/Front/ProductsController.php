<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use DB;
use App\{Category, Product, ProductsAttribute, Cart, Country, Coupon, DeliveryAddress, Sms,Order, OrdersProduct, ShippingCharge, User};
use Illuminate\Support\Facades\Mail;

class ProductsController extends Controller
{
    public function listing(Request $request){
        Paginator::useBootstrap();
        if($request->ajax()){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            $url = $data['url'];
            $categoryCount = Category::where(['url'=>$url,'status'=>1])->count();
            if($categoryCount>0){
                $categoryDetails = Category::catDetails($url);
                $categoryProducts = Product::with('brand')->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1);

                // IF fabric filter is selected
                if (isset($data['fabric']) && !empty($data['fabric'])) {
                    $categoryProducts->whereIn('products.fabric',$data['fabric']);
                }

                // IF sleeve filter is selected
                if (isset($data['sleeve']) && !empty($data['sleeve'])) {
                    $categoryProducts->whereIn('products.sleeve',$data['sleeve']);
                }

                // IF pattern filter is selected
                if (isset($data['pattern']) && !empty($data['pattern'])) {
                    $categoryProducts->whereIn('products.pattern',$data['pattern']);
                }

                // IF fit filter is selected
                if (isset($data['fit']) && !empty($data['fit'])) {
                    $categoryProducts->whereIn('products.fit',$data['fit']);
                }

                // IF occasion filter is selected
                if (isset($data['occasion']) && !empty($data['occasion'])) {
                    $categoryProducts->whereIn('products.occasion',$data['occasion']);
                }

                // IF sort option selected by user
                if(isset($data['sort']) && !empty($data['sort'])){
                    if($data['sort'] == "product_latest"){
                        $categoryProducts->orderBy('id', 'Desc');
                    }else if($data['sort'] == "product_name_a_z"){
                        $categoryProducts->orderBy('product_name', 'Asc');
                    }else if($data['sort'] == "product_name_a_z"){
                        $categoryProducts->orderBy('product_name', 'Desc');
                    }else if($data['sort'] == "price_lowest"){
                        $categoryProducts->orderBy('product_price', 'Asc');
                    } else if($data['sort'] == "price_highest"){
                        $categoryProducts->orderBy('product_price', 'Desc');
                    }
                }else{
                    $categoryProducts->orderBy('id', 'Desc');
                }

                $categoryProducts = $categoryProducts->paginate(30);

                return view('front.products.ajax_products_listing')->with(compact('categoryDetails','categoryProducts','url'));

            }else{
                abort(404);
            }

        }else{
            $url = Route::getFacadeRoot()->current()->uri();
            $categoryCount = Category::where(['url'=>$url,'status'=>1])->count();
            if($categoryCount>0){
                $categoryDetails = Category::catDetails($url);
                $categoryProducts = Product::with('brand')->whereIn('category_id', $categoryDetails['catIds'])->where('status', 1);
                $categoryProducts = $categoryProducts->paginate(3);

                // Product Filters
                $productFilters = Product::productFilters();
                $fabricArray = $productFilters['fabricArray'];
                $sleeveArray = $productFilters['sleeveArray'];
                $patternArray = $productFilters['patternArray'];
                $fitArray =  $productFilters['fitArray'];
                $occasionArray = $productFilters['occasionArray'];

                $page_name = "listing";

                return view('front.products.listing')->with(compact('categoryDetails','categoryProducts','url', 'fabricArray','sleeveArray','patternArray','fitArray','occasionArray', 'page_name'));

            }else{
                abort(404);
            }

        }
    }

    public function detail($id){
        $productDetails = Product::with(['category', 'brand', 'attributes'=>function($query){$query->where('status',1);}, 'images'])->find($id)->toArray();
        $total_stock = ProductsAttribute::where('product_id',$id)->sum('stock');
        $relatedProducts = Product::where('category_id', $productDetails['category']['id'])->where('id', '!=', $id)->limit(3)->inRandomOrder()->get()->toArray();
        $groupProducts = array();
        if(!empty($productDetails['group_code'])){
            $groupProducts = Product::select('id','main_image')->where('id','!=',$id)->where(['group_code' => $productDetails['group_code'],'status'=>1])->get()->toArray();
        }
        return view('front.products.detail')->with(compact('productDetails', 'total_stock', 'relatedProducts','groupProducts'));
    }

    public function getProductPrice(Request $request){
        if($request->ajax()){
            $data = $request->all();
            // $getProductPrice = ProductsAttribute::where(['product_id'=>$data['product_id'],'size'=>$data['size']])->first();
            $getDiscountedAttrPrice = Product::getDiscountedAttrPrice($data['product_id'],$data['size']);
            // echo "<pre>"; print_r($getDiscountedAttrPrice); die;

            return $getDiscountedAttrPrice;
        }
    }

    public function addtoCart(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            // Check Product Stock is avaible or not
            $getProductStock = ProductsAttribute::where(['product_id'=>$data['product_id'], 'size'=>$data['size']])->first()->toArray();
            if($getProductStock['stock']<$data['quantity']){
                $message = "Reuqired Quantity is not avaible";
                session::flash('error_message', $message);
                return redirect()->back();
            }

            // Generate Session Id id not exists
            $session_id = Session::get('session_id');
            if(empty($session_id)){
                $session_id = Session::getId();
                Session::put('session_id',$session_id);
            }

            // Check product if already exists in User cart
            if(Auth::check()){
                $countProducts = Cart::where(['product_id'=>$data['product_id'], 'size'=>$data['size'], 'user_id'=>Auth::user()->id])->count();
            }else{
                $countProducts = Cart::where(['product_id'=>$data['product_id'], 'size'=>$data['size'], 'session_id'=>Session::get('session_id')])->count();
            }

            if($countProducts>0){
                $message = "Product has been exists in cart!";
                session::flash('error_message',$message);
                return redirect()->back();
            }

            if(Auth::check()){
                $user_id = Auth::user()->id;
            } else {
                $user_id = 0;
            }

            // Save Product in cart
            // Cart::insert(['session_id'=>$session_id, 'product_id'=>$data['product_id'], 'size'=>$data['size'],'quantity'=>$data['quantity']]);
            $cart = New Cart;
            $cart->user_id = $user_id;
            $cart->session_id = $session_id;
            $cart->product_id = $data['product_id'];
            $cart->size = $data['size'];
            $cart->quantity = $data['quantity'];
            $cart->save();

            $message = "Product has been added in cart!";
            session::flash('success_message',$message);
            return redirect('cart');
        }
    }

    public function cart(){
        $userCartItems = Cart::userCartItems();
        // $session_id = Session::getId();
        // dd($session_id);
        // echo "<pre>"; print_r($userCardItems); die;
        return view('front.products.cart')->with(compact('userCartItems'));
    }

    public function updateCartItemQty(Request $request){
        if($request->ajax()){
            $data = $request->all();

            // Get Cart Details
            $cartDetails = Cart::find($data['cartid']);

            // Get Avaible Product Stock
            $availableStock = ProductsAttribute::select('stock')->where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size']])->first()->toArray();

            // Check Stock is avaible or not
            if($data['qty']>$availableStock['stock']){
                $userCartItems = Cart::userCartItems();
                return response()->json([
                    'status'=>false,
                    'message'=>'Product Stock is not avaible',
                    'view'=>(String)View::make('front.products.cart_items')->with(compact('userCartItems'))
                ]);
            }

            //  Check Size Avaible
            $availableSize = ProductsAttribute::where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size'],'status'=>1])->count();

            // Check Size is avaible or not
            if($availableSize==0){
                $userCartItems = Cart::userCartItems();
                return response()->json([
                        'status'=>false,
                        'message'=>'Product Size is not avaible',
                        'view'=>(String)View::make('front.products.cart_items')->with(compact('userCartItems'))
                ]);
            }

            Cart::where('id',$data['cartid'])->update(['quantity'=>$data['qty']]);
                $userCartItems = Cart::userCartItems();
                $totalCartItems = totalCartItems();
                return response()->json([
                        'status'=>true,
                        'totalCartItems'=>$totalCartItems,
                        'view'=>(String)View::make('front.products.cart_items')->with(compact('userCartItems'))
                ]);
        }
    }

    public function deleteCartItem(Request $request){
        if($request->ajax()){
            $data = $request->all();
            Cart::where('id',$data['cartid'])->delete();
            $userCartItems = Cart::userCartItems();
            $totalCartItems = totalCartItems();
                return response()->json([
                    'totalCartItems'=>$totalCartItems,
                    'view'=>(String)View::make('front.products.cart_items')->with(compact('userCartItems'))
                ]);
        }
    }


    public function applyCoupon(Request $request){
        if($request->ajax()){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            $userCartItems = Cart::userCartItems();
            $couponCount = Coupon::where('coupon_code', $data['code'])->count();
            if($couponCount==0){
                $userCartItems = Cart::userCartItems();
                $totalCartItems = totalCartItems();
                Session::forget('couponCode');
                Session::forget('couponAmount');
                return response()->json([
                    'status'=>false,
                    'message'=>'This coupon is not valid',
                    'totalCartItems' => $totalCartItems,
                    'view'=>(String)View::make('front.products.cart_items')->with(compact('userCartItems'))
                ]);
            }else{
                // Check for other coupon conditions

                // Get Coupon Details
                $couponDetails = Coupon::where('coupon_code', $data['code'])->first();

                // Check if coupon is Inactive
                if($couponDetails->status==0){
                    $message = "This coupon is not active!";
                }

                // Check if coupon is Expired
                $expiry_date = $couponDetails->expiry_date;
                $current_date = date('Y-m-d');
                if($expiry_date < $current_date){
                    $message = "This coupon is expired!";
                }

                // Check if coupon is for single or Multiple Times
                if($couponDetails->coupon_type == "Single Times"){
                    // Check in Orders table if coupon already availed by the user
                    $couponCount = Order::qhere(['coupon_code' => $data['code'],'user_id'=>Auth::user()->id])->count();
                    if($couponCount >= 1){
                        $message = "This coupon code is already availed by you!";
                    }
                }

                // Check if coupon is from selected categories
                // get all selected categories from coupon
                $catArr = explode(",",$couponDetails->categories);

                // Get Cart Items
                $userCartItems = Cart::userCartItems();
                // echo "<pre>"; print_r($userCartItems); die;

                // Check if coupon belongs to logged in user
                // Get all selected user of coupon
                if(!empty($couponDetails->users)){
                    $usersArr = explode(",", $couponDetails->users);
                    // Get User ID's of all selected users
                    foreach ($usersArr as $key => $user){
                        $getUserID = User::select('id')->where('email', $user)->first()->toArray();
                        $userID[] = $getUserID['id'];
                    }
                }

                // Get Cart Total Amount
                $total_amount = 0;
                foreach ($userCartItems as $key => $item){
                    // Check if any Item belong to Coupon category
                    if(!in_array($item['product']['category_id'], $catArr)){
                        $message = 'This coupon code is not for one of the selected product!';
                    }
                    if(!empty($couponDetails->users)){
                        if(!in_array($item['user_id'],$userID)){
                            $message = 'This coupon code is not for you';
                        }
                    }

                    $attrPrice = Product::getDiscountedAttrPrice($item['product_id'], $item['size']);
                    $total_amount = $total_amount + ($attrPrice['final_price'] * $item['quantity']);
                }


                if(isset($message)){
                    $userCartItems = Cart::userCartItems();
                    $totalCartItems = totalCartItems();
                    return response()->json([
                        'status'=>false,
                        'message'=>$message,
                        'totalCartItems'=>$totalCartItems,
                        'view'=>(String)View::make('front.products.cart_items')->with(compact('userCartItems'))
                    ]);
                }else{
                    if($couponDetails->amount_type == "Fixed"){
                        $couponAmount = $couponDetails->amount;
                    }else{
                        $couponAmount = $total_amount * ($couponDetails->amount/100);
                    }
                    $grand_total = $total_amount - $couponAmount;

                    // Add Coupon Code & Amount in Session Variables
                    Session::put('couponAmount',$couponAmount);
                    Session::put('couponCode' ,$data['code']);

                    $message = "Coupon code successfully apllied. You are availing discount!";
                    $userCartItems = Cart::userCartItems();
                    $totalCartItems = totalCartItems();

                    return response()->json([
                        'status'=>true,
                        'message'=>$message,
                        'totalCartItems'=>$totalCartItems,
                        'couponAmount'=>$couponAmount,
                        'grand_total'=>$grand_total,
                        'view'=>(String)View::make('front.products.cart_items')->with(compact('userCartItems'))
                    ]);
                }
            }
        }
    }

    public function checkout(Request $request){

        $userCartItems = Cart::userCartItems();
        if(count($userCartItems)==0){
            $message = "Shopping cart is empty! Please Add products to chekcout";
            Session::put('error_message',$message);
            return redirect('cart');
        }

        $total_price = 0;
        $total_weight = 0;
        foreach($userCartItems as $item){
            $prodcut_weight = $item['product']['product_weight'];
            $total_weight = $total_weight + $prodcut_weight;
            $attrPrice = Product::getDiscountedAttrPrice($item['product_id'],$item['size']);
            $total_price = $total_price + ($attrPrice['final_price'] * $item['quantity']);
        }

        $deliveryAddresses = DeliveryAddress::deliveryAdress();
        foreach ($deliveryAddresses as $key => $value){
            $shippingCharges = ShippingCharge::getShippingCharges($total_weight, $value['country']);
            $deliveryAddresses[$key]['shipping_charges'] = $shippingCharges;
            // Check if delivery Pincode exist in COD pincodes list
            $deliveryAddresses[$key]['codpincodeCount'] = DB::table('cod_pincodes')->where('pincode',$value['pincode'])->count();
            // Check ifndelivery Pincode exists in Prepaid Pincodes list
            $deliveryAddresses[$key]['prepaidpincodeCount'] = DB::table('prepaid_pincodes')->where('pincode', $value['pincode'])->count();
        }

        if($request->isMethod('post')){
            $data = $request->all();

            // Website Security Checks

            // Fetch User Cart Items
            foreach($userCartItems as $key => $cart){
                // Prevent Disabled product to Order
                $product_status = Product::getProductStatus($cart['product_id']);
                if($product_status==0){
                    // Product::deleteCartProduct($cart['product_id']);
                    $message = $cart['product']['product_name']." is not available so removed from Cart";
                    session::flash('error_message',$message);
                    return redirect('/cart');
                }
                // Prevent Out of Stock Products to Order
                $product_stock = Product::getProductStock($cart['product_id'], $cart['size']);
                if($product_stock==0){
                    // Product::deleteCartProduct($cart['product_id']);
                    $message = $cart['product']['product_name']." is not available so removed from Cart";
                    session::flash('error_message',$message);
                    return redirect('/cart');
                }

                // Prevent Disabled or Delete Attribute
                $getAttributeCount = Product::getAttributeCount($cart['product_id'],$cart['size']);
                if($getAttributeCount==0){
                    // Product::deleteCartProduct($cart['product_id']);
                    $message = $cart['product']['product_name']." is not available so removed from Cart";
                    session::flash('error_message',$message);
                    return redirect('/cart');
                }

                // Prevent Disabled Categories Products to Order
                $category_status = Product::getCategoryStatus($cart['product']['category_id']);
                if($category_status==0){
                    // Product::deleteCartProduct($cart['product_id']);
                    $message = $cart['product']['product_name']." is not available so removed from Cart";
                    session::flash('error_message',$message);
                    return redirect('/cart');
                }
            }
            if(empty($data['address_id'])){
                $message = "Please select Delivery Address!";
                session::flash('error_message',$message);
                return redirect()->back();
            }
            if(empty($data['payment_gateway'])){
                $message = "Please select Payment Method!";
                session::flash('error_message',$message);
                return redirect()->back();
            }

            if($data['payment_gateway']=="COD"){
                $payment_method = "COD";
                $order_status = "New";
            }else{
                $payment_method = "Prepaid";
                $order_status = "Pending";
            }

            // Get Delivery Address from address_id
            $deliveryAddress = DeliveryAddress::where('id',$data['address_id'])->first()->toArray();

            // Get shipping charges
            $shipping_charges = ShippingCharge::getShippingCharges($total_weight, $deliveryAddress['country']);

            // Grand Total
            $grand_total = $total_price + $shipping_charges - Session::get('couponAmount');

            // Insert Grand Total
            Session::put('grand_total',$grand_total);

            DB::beginTransaction();

            // Insert Order Details
            $order = new Order;
            $order->user_id = Auth::user()->id;
            $order->name = $deliveryAddress['name'];
            $order->address = $deliveryAddress['address'];
            $order->city = $deliveryAddress['city'];
            $order->state = $deliveryAddress['state'];
            $order->country = $deliveryAddress['country'];
            $order->pincode = $deliveryAddress['pincode'];
            $order->mobile = $deliveryAddress['mobile'];
            $order->email = Auth::user()->email;
            $order->shipping_charges = $shipping_charges;
            $order->coupon_code = Session::get('couponCode');
            $order->coupon_amount = Session::get('couponAmount');
            $order->order_status = $order_status;
            $order->payment_method = $payment_method;
            $order->payment_gateway = $data['payment_gateway'];
            $order->grand_total = Session::get('grand_total');
            $order->save();

            // Get last Inserted Order Id
            $order_id = DB::getPdo()->lastInsertId();

            // Get User Cart Items
            $cartItems = Cart::where('user_id', Auth::user()->id)->get()->toArray();
            foreach($cartItems as $key =>$item){
                $cartItem = new OrdersProduct;
                $cartItem->order_id = $order_id;
                $cartItem->user_id = Auth::user()->id;

                $getProductDetails = Product::select('product_code', 'product_name', 'product_color')->where('id', $item['product_id'])->first()->toArray();
                $cartItem->product_id = $item['product_id'];
                $cartItem->product_code = $getProductDetails['product_code'];
                $cartItem->product_name = $getProductDetails['product_name'];
                $cartItem->product_color = $getProductDetails['product_color'];
                $cartItem->product_size = $item['size'];
                $getDiscountAttrPrice = Product::getDiscountedAttrPrice($item['product_id'], $item['size']);
                $cartItem->product_price = $getDiscountAttrPrice['final_price'];
                $cartItem->product_qty = $item['quantity'];
                $cartItem->save();

                if($data['payment_gateway']=="COD"){
                    // Reduce Stock Script Starts
                    $getProductStock = ProductsAttribute::where(['product_id'=>$item['product_id'],'size'=>$item['size']])->first()->toArray();
                    $newStock = $getProductStock['stock'] - $item['quantity'];
                    ProductsAttribute::where(['product_id'=>$item['product_id'],'size'=>$item['size']])->update(['stock' => $newStock]);
                }
            }

            // Insert Order id in Session Variable
            Session::put('order_id',$order_id);

            DB::commit();

            if($data['payment_gateway']=="COD"){

                // Send Order SMS
                // $message = "Dear CUstomer, your order ".$order_id."has been successfully placed with Ecom. We will intimate you once your order is shipped.";
                // $mobile = Auth::user()->mobile;
                // Sms::sendSms($message,$mobile);

                $orderDetails = Order::with('orders_products')->where('id',$order_id)->first()->toArray();

                // Send Order Email
                // $email = Auth::user()->email;
                // $messageData = [
                //     'email' => $email,
                //     'name' => Auth::user()->name,
                //     'order_id' => $order_id,
                //     'orderDetails' => $orderDetails
                // ];
                // Mail::send('emails.order',$messageData,function($message) use($email){
                //     $message->to($email)->subject('Order Placed - Ecom');
                // });

                return redirect('/thanks');
            }else if($data['payment_gateway'] =="Paypal"){
                return redirect('paypal');
            } if($data['payment_gateway'] =="Payumoney"){
                return redirect('payumoney');
            }else{
                echo "Prepare Method Coming Soon"; die;
            }


        }

        return view('front.products.checkout')->with(compact('userCartItems', 'deliveryAddresses', 'total_price'));
    }

    public function thanks(){
        if(Session::has('order_id')){
            // Empty Cart
            Cart::where('user_id', Auth::user()->id)->delete();
            return view('front.products.thanks');
        }else{
            return redirect('/cart');
        }
    }

    public function addEditDeliveryAddress(int $id=null,Request $request){

        if($id==""){
            // Add Delivery Address
            $title = "Add Delivery Address";
            $address = new DeliveryAddress;
            $message = "Delivery Address added successfully!";

        }else{
            // Edit Delivery Address
            $title = "Edit Delivery Address";
            $address = DeliveryAddress::find($id);
            $message = "Delivery Address updated successfully!";
        }

        if($request->isMethod('post')){
            $data = $request->all();

            $rules = [
                'name' => 'required|regex:/^[\pL\s\-]+$/u',
                'address' => 'required',
                'city' => 'required|regex:/^[\pL\s\-]+$/u',
                'state' => 'required|regex:/^[\pL\s\-]+$/u',
                'country' => 'required',
                'pincode' => 'required|numeric',
                'mobile' => 'required|numeric',
            ];
            $customMessages = [
                'name.required' => 'Name is required',
                'name.regex' =>'Valid Name is required',
                'address.required' => 'Address is required',
                'city.required' => 'City is required',
                'city.regex' =>'Valid City is required',
                'state.required' => 'State is required',
                'state.regex' =>'Valid State is required',
                'country.required' => 'Name is required',
                'pincode.required' => 'Pincode is required',
                'pincode.numeric' => 'Valid Pincode is required',
                'mobile.required' =>'Mobile is required',
                'mobile.numeric' => 'Valid Mobile is required',
            ];
            $this->validate($request,$rules,$customMessages);

            $address->user_id = Auth::user()->id;
            $address->name = $data['name'];
            $address->address = $data['address'];
            $address->city = $data['city'];
            $address->state = $data['state'];
            $address->country = $data['country'];
            $address->mobile = $data['mobile'];
            $address->pincode = $data['pincode'];
            $address->status = 1;
            $address->save();
            $message = "Your account details has been updated successfully!";
            Session::put('success_message', $message);
            return redirect('check-out');

        }
        $countries = Country::where('status',1)->get()->toArray();
        return view('front.products.add_edit_delivery_address')->with(compact('address', 'countries', 'title'));
    }

    public function deleteDeliveryAddress($id){
        DeliveryAddress::where('id',$id)->delete();
        $message = "Delivery Address deleted successfully!";
        Session::put('success_message', $message);
        return redirect()->back();
    }

    public function checkPincode(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if(\is_numeric($data['pincode']) && $data['pincode'] > 0 && $data['pincode']==round($data['pincode'], 0)){

                $codPincodeCount = DB::table('cod_pincodes')->where('pincode',$data['pincode'])->count();
                $prePaidpincodeCount = DB::table('prepaid_pincodes')->where('pincode', $data['pincode'])->count();

                if($codPincodeCount==0 && $prePaidpincodeCount == 0){
                    echo "This pincode is not available for delivery"; die;
                }else{
                    echo "This pincode is available for delivery"; die;
                }
            } else {
                echo "Please enter valid pincode"; die;
            }
        }
    }


}
