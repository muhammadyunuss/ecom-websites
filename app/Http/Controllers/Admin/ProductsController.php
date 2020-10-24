<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Section;
use App\Product;
use App\Category;
use App\Brand;
use App\ProductsAttribute;
use App\ProductsImage;
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
            if($data['status']=="Active"){
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

        $message = "Product Deleted successfully!";
        session::flash('success_message',$message);
        return redirect()->back();
    }

    public function addEditProduct(Request $request, $id=null){
        if($id==""){
            $title = "Add Product";
            $product = new Product;
            $productdata = array();
            $message = "Product added successfully!";
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
                'brand_id' => 'required',
                'product_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'product_code' => 'required|regex:/^[\w-]*$/',
                'product_price' => 'required|numeric',
                'product_color' => 'required|regex:/^[\pL\s\-]+$/u',
            ];
            $customMessages = [
                'category_id.required' => 'Category is required',
                'product_name.required' => 'Product Name is required',
                'product_name.regex' => 'Valid Product Name is required',
                'product_code.required' => 'Product Code is required',
                'product_code.regex' => 'Valid Product Code is required',
                'product_price.required' =>'Product Price is required',
                'product_price.numeric' =>'Valid Product Price is required',
                'product_color.required' =>'Product Color is required',
                'product_color.regex' =>'Valid Product Color is required',
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

            if(empty($data['brand_id'])){
                $data['brand_id'] = "";
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
                    Image::make($image_tmp)->resize(1040,1200)->save($large_image_path); //W;1040 H:1200
                    // Upload Resize Medium and Small Image
                    Image::make($image_tmp)->resize(520,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(260,300)->save($small_image_path);
                    // Video Product Main Image in Products table
                    $product->main_image = $imageName;
                }
            }

            // Upload Product Video
            if(empty($data['product_video'])){
                $data['product_video'] = "";
                $product->product_video = $data['product_video'];
            }else if($request->hasFile('product_video')){
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
            $product->brand_id = $data['brand_id'];
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

        // Product Filters
        $productFilters = Product::productFilters();
        $fabricArray = $productFilters['fabricArray'];
        $sleeveArray = $productFilters['sleeveArray'];
        $patternArray = $productFilters['patternArray'];
        $fitArray =  $productFilters['fitArray'];
        $occasionArray = $productFilters['occasionArray'];

        // Section with Categories and Sub Categories
        $categories = Section::with('categories')->get();
        $categories = json_decode(json_encode($categories),true);

        // Get All Brands
        $brands = Brand::where('status',1)->get();
        $brands = json_decode(json_encode($brands),true);

        // echo "<pre>"; print_r($categories); die;

        return view('admin.products.add_edit_product')->with(compact('title','fabricArray','sleeveArray','patternArray','fitArray','occasionArray','categories','productdata','brands'));
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

        $message = "Image Product deleted successfully!";
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

        $message = "Video Product deleted successfully!";
        session::flash('success_message',$message);
        return redirect()->back();
    }

    public function addAttributes(Request $request ,$id){
        if($request->isMethod('post')){
            $data = $request->all();
            // dd($data);
            foreach ($data['sku'] as $key => $value) {
                if(!empty($value)){

                    // SKU already exist check
                    $attrCountSKU = ProductsAttribute::where(['sku'=>$value])->count();
                    if($attrCountSKU>0){
                        $message = 'SKU already exist. Please add another SKU!';
                        session::flash('error_message',$message);
                        return redirect()->back();
                    }

                    //
                    $attrCountSize = ProductsAttribute::where(['product_id'=>$id ,'size'=>$data['size'][$key]])->count();
                    if($attrCountSize>0){
                        $message = 'Size already exist. Please add another Size!';
                        session::flash('error_message',$message);
                        return redirect()->back();
                    }

                    $attribute = new ProductsAttribute;
                    $attribute->product_id = $id;
                    $attribute->sku = $value;
                    $attribute->size = $data['size'][$key];
                    $attribute->price = $data['price'][$key];
                    $attribute->stock = $data['stock'][$key];
                    $attribute->status = 1;
                    $attribute->save();
                }
            }

            $message = 'Product Attributes added successfully!';
            session::flash('success_message',$message);
            return redirect()->back();
        }

        $productdata = Product::select('id', 'product_name', 'product_code', 'product_color','product_price' ,'main_image')->with('attributes')->find($id);
        $productdata = json_decode(json_encode($productdata),true);

        $title = "Product Attributes";
        return view('admin.products.add_attributes')->with(compact('productdata','title'));
    }

    public function editAttributes(Request $request,$id){
        if($request->isMethod('post')){
            $data = $request->all();
            foreach ($data['attrId'] as $key => $attr) {
                if (!empty($attr)) {
                    ProductsAttribute::where(['id'=>$data['attrId'][$key]])->update(['price'=>$data['price'][$key], 'stock'=>$data['stock'][$key]]);
                }
            }

            $message = 'Product Attributes has been updated successfully!';
            session::flash('success_message',$message);
            return redirect()->back();
        }
    }

    public function updateAttributeStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }

            ProductsAttribute::where('id',$data['attribute_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'attribute_id'=>$data['attribute_id']]);
        }
    }

    public function deleteAttribute($id){
        ProductsAttribute::where('id',$id)->delete();

        $message = "Attribute has been deleted successfully!";
        session::flash('success_message',$message);
        return redirect()->back();
    }

    public function addImages(Request $request, $id){
        if ($request->isMethod('post')) {
            if($request->hasFile('image')){
                $images = $request->file('image');

                foreach ($images as $key => $image) {
                    $productImage = new ProductsImage;
                    $image_tmp = Image::make($image);

                    // Get Image Extension
                    $extension = $image->getClientOriginalExtension();
                    // Generate New Image Name
                    $imageName = rand(111,99999).time().'.'.$extension;
                    // Set Paths For small, medium and large Image
                    $large_image_path = 'images/product_images/large/'.$imageName;
                    $medium_image_path = 'images/product_images/medium/'.$imageName;
                    $small_image_path = 'images/product_images/small/'.$imageName;
                    // Upload Large Image
                    Image::make($image_tmp)->resize(1040,1200)->save($large_image_path); //W;1040 H:1200
                    // Upload Resize Medium and Small Image
                    Image::make($image_tmp)->resize(520,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(260,300)->save($small_image_path);
                    // Save Product Main Image in Products table
                    $productImage->image = $imageName;
                    $productImage->product_id = $id;
                    $productImage->status = 1;
                    $productImage->save();
                }

                $message = "Product Images has been added successfully!";
                session::flash('success_message',$message);
                return redirect()->back();
            }
        }
        $title = "Product Images";
        $productdata = Product::select('id', 'product_name', 'product_code', 'product_color','product_price' ,'main_image')->with('images')->find($id);
        $productdata = json_decode(json_encode($productdata),true);
        return view('admin.products.add_images')->with(compact('title','productdata'));
    }

    public function updateImageStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }

            ProductsImage::where('id',$data['image_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'image_id'=>$data['image_id']]);
        }
    }

    public function deleteImage($id){
        // Get Product Image
        $productImage = ProductsImage::select('image')->where('id',$id)->first();

        // Get Produk Image Path
        $small_image_path = 'images/product_images/small';
        $medium_image_path = 'images/product_images/medium';
        $large_image_path = 'images/product_images/large';

        // Delete Product Image From product_images folder if exist
        if(file_exists($small_image_path.$productImage->image)){
            unlink($small_image_path.$productImage->image);
        }

        if(file_exists($medium_image_path.$productImage->image)){
            unlink($medium_image_path.$productImage->image);
        }

        if(file_exists($large_image_path.$productImage->image)){
            unlink($large_image_path.$productImage->image);
        }

        // Hapus produk gambar dari kategori tabel
        ProductsImage::where('id',$id)->delete();

        $message = "Product image has been deleted successfully!";
        session::flash('success_message',$message);
        return redirect()->back();
    }
}
