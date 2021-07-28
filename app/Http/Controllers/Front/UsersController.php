<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Session;
use Auth;
use App\Cart;
use App\Country;
use App\Sms;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function loginRegister(){
        return view('front.users.login_register');
    }

    public function registerUser(Request $request){
        if($request->isMethod('post')){
            Session::forget('error_message');
            Session::forget('success_message');
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
                $user->status = 0;
                $user->save();

                // Send COnfirmation Email
                $email =$data['email'];
                $messageData = [
                    'email' => $data['email'],
                    'name' => $data['name'],
                    'code' => base64_encode($data['email']),
                ];

                Mail::send('emails.confirmation',$messageData, function($message) use($email){
                    $message->to($email)->subject('Confirm your E-Commerce Account');
                });

                // Redirect Back with Success Message
                $message = "Please confirm your email to activate your account!";
                Session::put('success_message',$message);
                return redirect()->back();

                /*
                if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){

                    // Update User Cart With user_id
                    if(!empty(Session::get('session_id'))){
                        $user_id = Auth::user()->id;
                        $session_id = Session::get('session_id');
                        Cart::where('session_id',$session_id)->update(['user_id' => $user_id]);
                    }


                    //  Send Register SMS
                    $message = "Deat Costumer, you have been successfully registered with E-com Websiter. Login to your account to access orders and avalable offers.";
                    $mobile =$data['mobile'];
                    Sms::sendSms($message, $mobile);



                    // Send Register Email
                    $email = $data['email'];
                    $messageData = ['name' => $data['name'],'mobile' => $data['mobile'], 'email' =>$data['email']];
                    Mail::send('emails.register',$messageData, function($message) use($email){
                        $message->to($email)->subject('Welcome to E-Commerce Website');
                    });


                    // echo "<pre>"; print_r(Auth::user()); die;
                    return redirect('casual-t-shirts');
                }
                */

            }
        }
    }

    public function confirmAccount($email){
        Session::forget('error_message');
        Session::forget('success_message');
        // Decode User Email
        $email = base64_decode($email);
        // Check User Email Exists
        $userCount = User::where('email',$email)->count();
        if($userCount>0){
            // User Email is already activated or not
            $userDetails = User::where('email',$email)->first();
            if($userDetails->status == 1){
                $message = "Your Email account is already activated. Please Login.";
                Session::put('error_message',$message);
                return redirect('login-register');
            }else{
                // Update User Status to 1 to ACtivated account
                User::where('email',$email)->update(['status'=>1]);

                /*  //  Send Register SMS
                $message = "Deat Costumer, you have been successfully registered with E-com Websiter. Login to your account to access orders and avalable offers.";
                $mobile =$data['mobile'];
                Sms::sendSms($message, $mobile);
                */

                // Send Register Email
                $messageData = ['name' => $userDetails['name'],'mobile' => $userDetails['mobile'], 'email' =>$userDetails['email']];
                Mail::send('emails.register',$messageData, function($message) use($email){
                    $message->to($email)->subject('Welcome to E-Commerce Website');
                });

                // Redirect to login.register page with success message
                $message = "Your Email account is activated. You can login now.";
                Session::put('success_message',$message);
                return redirect('login-register');
            }
        }else{
            abort(404);

        }
    }

    public function checkEmail(Request $request){
        // Check if email already exist
        $data = $request->all();
        $emailCount = User::where('email',$data['email'])->count();
        if($emailCount>0){
            return false;
        }else{
            return true;
        }
    }

    public function loginUser(Request $request){
        if($request->isMethod('post')){
            Session::forget('error_message');
            Session::forget('success_message');

            $data = $request->all();

            if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){

                //Check Email is activated or not
                $userStatus = User::where('email',$data['email'])->first();
                if($userStatus->status == 0){
                    Auth::logout();
                    $message = "Your Account is not activated yet! Please Confirm your email tok activate!";
                    Session::put('error_message', $message);
                    return redirect()->back();
                }
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

    public function forgotPassword(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            $emailCount = User::where('email', $data['email'])->count();
            if($emailCount == 0){
                $message = "Email does not exists!";
                Session::put('error_message', $message);
                Session::forget('success_message');
                return redirect()->back();
            }

            // generate Random Password
            $random_password = str_random(8);

            // endoce/secure password
            $new_password = bcrypt($random_password);

            // Update Password
            User::where('email',$data['email'])->update(['password'=>$new_password]);

            // get User Name
            $userName = User::select('name')->where('email',$data['email'])->first();

            // Send Forget Passowrd Email
            $email = $data['email'];
            $name = $userName->name;
            $messageData = [
                'email' => $email,
                'name' => $name,
                'password' => $random_password,
            ];
            Mail::send('emails.forgot_password', $messageData, function($message)use($email){
                $message->to($email)->subject('New Password = E-Commerce Website');
            });

            // Redirect to Login/Register Page with Success Message
            $message= "Please check your email for new Password!";
            Session::put('success_message', $message);
            Session::forget('error_message');

        }
        return view('front.users.forgot_password');
    }

    public function account(Request $request){
        $user_id = Auth::user()->id;
        $userDetails = User::find($user_id)->toArray();

        $countries = Country::where('status',1)->get()->toArray();

        if($request->isMethod('post')){
            $data = $request->all();

            Session::put('success_message');
            Session::forget('error_message');
            $rules = [
                'name' => 'required|regex:/^[\pL\s\-]+$/u',
                'mobile' => 'required|numeric',
            ];
            $customMessages = [
                'name.required' => 'Name is required',
                'name.regex' =>'Valid Name is required',
                'mobile.required' =>'Mobile is required',
                'mobile.numeric' => 'Valid Mobile is required',
            ];
            $this->validate($request,$rules,$customMessages);

            $user = User::find($user_id);
            $user->name = $data['name'];
            $user->address = $data['address'];
            $user->city = $data['city'];
            $user->state = $data['state'];
            $user->country = $data['country'];
            $user->mobile = $data['mobile'];
            $user->pincode = $data['pincode'];
            $user->email = $data['email'];
            $user->save();
            $message = "Your account details has been updated successfully!";
            Session::put('success_message', $message);
            return redirect()->back();
        }
        return view('front.users.account')->with(compact('userDetails','countries'));
    }

    public function chkUserPassword(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; \print_r($data); die;
            $user_id = Auth::User()->id;
            $chkPassword = User::select('password')->where('id', $user_id)->first();
            if(Hash::check($data['current_pwd'],$chkPassword->password)){
                return "true";
            }else{
                return "false";
            }
        }
    }

    public function updateUserPassword(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; \print_r($data); die;
            $user_id = Auth::User()->id;
            $chkPassword = User::select('password')->where('id', $user_id)->first();
            if(Hash::check($data['current_pwd'],$chkPassword->password)){
                $new_pwd = \bcrypt($data['new_pwd']);
                User::where('id', $user_id)->update(['password'=>$new_pwd]);
                $message = "Password updated successfuly!";
                Session::put('success_message', $message);
                Session::forget('error_message');
                return redirect()->back();
            }else{
                $message = "Current Password is Invalid!";
                Session::put('error_message', $message);
                Session::forget('success_message');
                return redirect()->back();
            }
        }
    }
}
