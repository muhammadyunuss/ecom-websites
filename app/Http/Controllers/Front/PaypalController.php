<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\{Order, Cart};
use Session;
use Auth;

class PaypalController extends Controller
{
    public function paypal(){
        if(Session::has('order_id')){
            $orderDetails = Order::where('id',Session::get('order_id'))->first()->toArray();
            $nameArr = explode(' ',$orderDetails['name']);
            return view('front.paypal.paypal')->with(compact('orderDetails','nameArr'));
        }else{
            return redirect('/cart');
        }
    }

    public function sucess(){
        if(Session::has('order_id')){
            // Empty Cart
            Cart::where('user_id', Auth::user()->id)->delete();
            return view('front.paypal.success');
        }else{
            return redirect('/cart');
        }
    }

    public function fail(){
        return view('front.paypal.fail');
    }

    public function ipn(Request $request){
        $data = $request->all();
        if($data['payment_status']=="Completed"){

            // Proccess the Order
            $order_id = Session::get('order_id');

            // Update Order Status to Paid
            Order::where('id',$order_id)->update(['order_id'=>'Paid']);

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

        }
    }

}
