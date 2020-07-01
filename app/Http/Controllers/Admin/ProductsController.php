<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Section;
use App\Product;
use App\Category;
use Session;
use Image;

class ProductsController extends Controller
{
    public function products(){
        Session::put('page','products');
        $products = Product::with(['category'=>function($query){
            $query->select('id','category_name');
        },'section'=>function($query){
            $query->select('id','name');
        }])->get();
        // $products = json_decode(json_encode($products));
        // echo "<pre>"; print_r($products); die; 
        return view('admin.products.products')->with(compact('products'));
    }

    public function updateProductStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if($data['status']=="Aktif"){
                $status = 0;
            }else{
                $status = 1;
            }

            Product::where('id',$data['product_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'product_id'=>$data['product_id']]);
        }
    }

    public function deleteProduct($id){
        Product::where('id',$id)->delete();

        $message = "Produk berhasil Di Hapus!";
        session::flash('success_message',$message);
        return redirect()->back();
    }

    public function addEditProduct(Request $request, $id=null){
        if($id==""){
            $title = "Add Product";
            $product = new Product;
            $productdata = array();
            $message = "Product added successfuly!";
        }else{
            $title = "Edit Product";
            $productdata = Product::find($id);
            $productdata =json_decode(json_encode($productdata),true);
            $product = Product::find($id);
            $message = "Prodcut updated successfully!";
        }

        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;

            // Product Validations
            $rules = [
                'category_id' => 'required',
                'product_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'product_code' => 'required|regex:/^[\w-]*$/',
                'product_price' => 'required|numeric',
                'product_color' => 'required|regex:/^[\pL\s\-]+$/u',
            ];
            $customMessages = [
                'category_id.required' => 'Nama Kategori Wajib Di Isi',
                'product_name.required' => 'Nama Produk Wajib Di Isi',
                'product_name.regex' => 'Diperlukan Nama Produk Yang Benar',
                'product_code.required' => 'Nama Kode Produk Wajib Di Isi',
                'product_code.regex' => 'Diperlukan Kode Produk Yang Benar',
                'product_price.required' =>'Harga Produk Wajib Di Isi',
                'product_price.numeric' =>'Diperlukan Harga Produk Yang Benar',
                'product_color.required' =>'Warna Produk Wajib Di Isi',
                'product_color.regex' =>'Diperlukan Warna Produk Yang Benar',
            ];
            $this->validate($request,$rules,$customMessages);

            
            if(empty($data['is_featured'])){
                $is_featured = "No";
            }else{
                $is_featured = "Yes";
            }

            if(empty($data['fabric'])){
                $data['fabric'] = "";
            }

            if(empty($data['pattern'])){
                $data['pattern'] = "";
            }

            if(empty($data['sleeve'])){
                $data['sleeve'] = "";
            }
            
            if(empty($data['fit'])){
                $data['fit'] = "";
            }

            if(empty($data['occasion'])){
                $data['occasion'] = "";
            }

            if(empty($data['meta_title'])){
                $data['meta_title'] = "";
            }

            if(empty($data['meta_keywords'])){
                $data['meta_Keywords'] = "";
            }

            if(empty($data['meta_description'])){
                $data['meta_description'] = "";
            }

            if(empty($data['product_video'])){
                $data['product_video'] = "";
            }

            if(empty($data['main_image'])){
                $data['main_image'] = "";
            }

            if(empty($data['description'])){
                $data['description'] = "";
            }

            if(empty($data['wash_care'])){
                $data['wash_care'] = "";
            }

            if(empty($data['meta_keywords'])){
                $data['meta_keywords'] = "";
            }

            if(empty($data['product_discount'])){
                $data['product_discount'] = "0";
            }

            // Upload Product Image
            if($request->hasFile('main_image')){
                $image_tmp = $request->file('main_image');
                if($image_tmp->isValid()){
                    // Get Original Image Name
                    $image_name = $image_tmp->getClientOriginalName();
                    // Get Image Extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate New Image Name
                    $imageName = $image_name.'-'.rand(111,99999).'.'.$extension;
                    // Set Paths For small, medium and large Image
                    $large_image_path = 'images/product_images/large/'.$imageName;
                    $medium_image_path = 'images/product_images/medium/'.$imageName;
                    $small_image_path = 'images/product_images/small/'.$imageName;
                    // Upload Large Image
                    Image::make($image_tmp)->save($large_image_path); //W;1040 H:1200
                    // Upload Resize Medium and Small Image
                    Image::make($image_tmp)->resize(520,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(260,300)->save($small_image_path);
                    // Video Product Main Image in Products table
                    $product->main_image = $imageName;
                }
            }

            // Upload Product Video
            if($request->hasFile('product_video')){
                $video_tmp = $request->file('product_video');
                if($video_tmp->isValid()){
                    // Upload Video
                    $video_name = $video_tmp->getClientOriginalName();
                    $extension = $video_tmp->getClientOriginalExtension();
                    $videoName = $video_name.'-'.rand().'.'.$extension;
                    $video_path = 'videos/product_videos/';
                    $video_tmp->move($video_path,$videoName);
                    // Save Video in product table
                    $product->product_video = $videoName;
                }
            }

            // Menyimpan Produk detail
            $categoryDetails = Category::find($data['category_id']);
            // echo "<pre>"; print_r($categoryDetails); die;
            $product->section_id = $categoryDetails['section_id'];
            $product->category_id = $data['category_id'];
            $product->product_name = $data['product_name'];
            $product->product_code = $data['product_code'];
            $product->product_color = $data['product_color'];
            $product->product_discount = $data['product_discount'];
            $product->product_price = $data['product_price'];
            $product->product_weight = $data['product_weight'];
            $product->description = $data['description'];
            $product->wash_care = $data['wash_care'];
            $product->fabric = $data['fabric'];
            $product->fit = $data['fit'];
            $product->pattern = $data['pattern'];
            $product->sleeve = $data['sleeve'];
            $product->occasion = $data['occasion'];
            $product->meta_title = $data['meta_title'];
            $product->meta_keywords = $data['meta_keywords'];
            $product->meta_description = $data['meta_description'];
            $product->is_featured = $is_featured;
            $product->status = 1;
            $product->save();
            session::flash('success_message',$message);
            return redirect('admin/products');
        }

        // Filter Arrays
        $fabricArray = array('Cotton','Polyester','Wool');
        $sleeveArray = array('Full Sleeve','Half Sleeve','Short Sleeve','Sleeveless');
        $patternArray = array('Checked','Plain','Printed','Self','Solid');
        $fitArray = array('Regular','Slim');
        $occasionArray = array('Casual','Formal');

        // Section with Categories and Sub Categories
        $categories = Section::with('categories')->get();
        $categories = json_decode(json_encode($categories),true);
        // echo "<pre>"; print_r($categories); die; 

        return view('admin.products.add_edit_product')->with(compact('title','fabricArray','sleeveArray','patternArray','fitArray','occasionArray','categories','productdata'));
    }

    public function deleteProductImage($id){
        // Get Product Image
        $productImage = Product::select('main_image')->where('id',$id)->first();

        // Get Produk Image Path
        $small_image_path = 'images/product_images/small';
        $medium_image_path = 'images/product_images/medium';
        $large_image_path = 'images/product_images/large';

        // Delete Product Image From product_images folder if exist
        if(file_exists($small_image_path.$productImage->main_image)){
            unlink($small_image_path.$productImage->main_image);
        }

        if(file_exists($medium_image_path.$productImage->main_image)){
            unlink($medium_image_path.$productImage->main_image);
        }

        if(file_exists($large_image_path.$productImage->main_image)){
            unlink($large_image_path.$productImage->main_image);
        }

        // Hapus produk gambar dari kategori tabel
        Product::where('id',$id)->update(['main_image'=>'']);

        $message = "Gambar Produk berhasil Di Hapus!";
        session::flash('success_message',$message);
        return redirect()->back();
    }

    public function deleteProductVideo($id){
        // Get Product Image
        $productVideo = Product::select('product_video')->where('id',$id)->first();

        // Get Produk Image Path
        $product_video_path = 'images/product_images/small';

        // Delete Product Image From product_images folder if exist
        if(file_exists($product_video_path.$productVideo->product_video)){
            unlink($product_video_path.$productVideo->product_video);
        }

        // Hapus produk gambar dari kategori tabel
        Product::where('id',$id)->update(['product_video'=>'']);

        $message = "Video Produk berhasil Di Hapus!";
        session::flash('success_message',$message);
        return redirect()->back();
    }
}
