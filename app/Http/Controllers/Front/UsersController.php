<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Session;
use Auth;
use App\Cart;
use App\Sms;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function loginRegister(){
        return view('front.users.login_register');
    }

    public function registerUser(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            // Check if User already exist
            $userCount = User::where('email',$data['email'])->count();
            if($userCount>0){
                $message = "Email already exist!";
                session::flash('error_message',$message);
                return redirect()->back();
            }else{
                // Register the User
                $user = new User;
                $user->name = $data['name'];
                $user->mobile = $data['mobile'];
                $user->email = $data['email'];
                $user->password = bcrypt($data['password']);
                $user->status = 1;
                $user->save();

                if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){

                    // Update User Cart With user_id
                    if(!empty(Session::get('session_id'))){
                        $user_id = Auth::user()->id;
                        $session_id = Session::get('session_id');
                        Cart::where('session_id',$session_id)->update(['user_id' => $user_id]);
                    }

                    /*
                    //  Send Register SMS
                    $message = "Deat Costumer, you have been successfully registered with E-com Websiter. Login to your account to access orders and avalable offers.";
                    $mobile =$data['mobile'];
                    Sms::sendSms($message, $mobile);
                    */

                   /*
                    // Send Register Email
                    $email = $data['email'];
                    $messageData = ['name' => $data['name'],'mobile' => $data['mobile'], 'eamil' =>$data['email']];
                    Mail::send('emails.register',$messageData, function($message) use($email){
                        $message->to($email)->subject('Welcome to E-Commerce Website');
                    });
                    */

                    // echo "<pre>"; print_r(Auth::user()); die;
                    return redirect('cart');
                }

            }
        }
    }

    public function checkEmail(Request $request){
        // Check if email already exist
        $data = $request->all();
        $emailCount = User::where('email',$data['email'])->count();
        if($emailCount>0){
            return false;
        }else{
            return true; die;
        }
    }

    public function loginUser(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();

            if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){

                // Update User Cart With user_id
                if(!empty(Session::get('session_id'))){
                    $user_id = Auth::user()->id;
                    $session_id = Session::get('session_id');
                    Cart::where('session_id',$session_id)->update(['user_id' => $user_id]);
                }

                return redirect('/cart');
            }else{
                $message = "Invalid Username and Password";
                Session::flash('error_message', $message);
                return \redirect()->back();
            }
        }
    }

    public function logoutUser(){
        Auth::logout();
        return redirect('/');
    }
}
