<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Banner;
use Session;
use Image;

class BannersController extends Controller
{
    public function banners(){
        Session::put('page','banners');
        $banners = Banner::get()->toArray();
        return view('admin.banners.banners')->with(compact('banners'));
    }

    public function addeditBanner(Request $request, $id=null){
        if($id==""){
            $banner = new Banner;
            $title = "Add Banner Image";
            $message = "Banner added sucessfully!";
        }else{
            $banner = Banner::find($id);
            $title = "Edit Banner Image";
            $message = "Banner updated successfully";
        }

        if($request->isMethod('post')){
            $data = $request->all();

            if(empty($data['title'])){
                $banner->title = "";
            }

            if(empty($data['link'])){
                $banner->link = "";
            }

            if(empty($data['alt'])){
                $banner->alt = "";
            }

            $banner->link = $data['link'];
            $banner->alt = $data['alt'];
            $banner->title = $data['title'];
            $banner->status = 1;

            // Upload Product Image
           if($request->hasFile('image')){
                $image_tmp = $request->file('image');
                if($image_tmp->isValid()){
                    // Get Original Image Name
                    $image_name = $image_tmp->getClientOriginalName();
                    // Get Image Extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate New Image Name
                    $imageName = $image_name.'-'.rand(111,99999).'.'.$extension;
                    // Set Paths For small, medium and large Image
                    $banner_image_path = 'images/banner_images/'.$imageName;
                    // Upload Banner Image after Resize
                    Image::make($image_tmp)->resize(1170,480)->save($banner_image_path); //W;1170 H:480
                    // Save Banner Image in Banners table
                    $banner->image = $imageName;
                }
            }

            $banner->save();
            session::flash('success_message',$message);
            return redirect('admin/banners');
        }

        return view('admin.banners.add_edit_banner')->with(compact('title','banner'));
    }

    public function updateBannerStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }

            Banner::where('id',$data['banner_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'banner_id'=>$data['banner_id']]);
        }
    }

    public function deleteBanner($id){
        // Get Banner Image
        $bannerImage = Banner::where('id',$id)->first();

        // Get Banner Image Path
        $banner_image_path = 'images/banner_images/';

        // Get Banner Image if exist in banners folder
        if(file_exists($banner_image_path.$bannerImage->image)){
            unlink($banner_image_path.$bannerImage->image);
        }

        Banner::where('id',$id)->delete();

        session()->flash('success_message', 'Banner deleted successfully!');
        return redirect()->back();
    }
}
