<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Session;

class UsersController extends Controller
{
    public function users(){
        Session::put('page', 'users');
        $users = User::get();
        return view('admin.users.users')->with(compact('users'));
    }

    public function updateUserStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }

            User::where('id',$data['user_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'user_id'=>$data['user_id']]);
        }
    }
}
