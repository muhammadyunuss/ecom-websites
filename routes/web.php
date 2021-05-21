<?php

use Illuminate\Support\Facades\Route;
use App\Category;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix(' admin')->namespace('Admin')->group(function(){
    // Semua Admin yang akan di definisikan disini :-

    Route::match(['get','post'],'/','AdminController@login');
    Route::group(['middleware'=>['admin']],function(){
        Route::get('clock','AdminController@clock');

        Route::get('dashboard','AdminController@dashboard');
        Route::get('settings','AdminController@settings');
        Route::get('logout','AdminController@logout');
        Route::post('check-current-pwd','AdminController@chkCurrentPassword');
        Route::post('update-current-pwd','AdminController@updateCurrentPassword');
        Route::match(['get','post'],'update-admin-details','AdminController@updateAdminDetails');

        // Sections
        Route::get('sections','SectionController@sections');
        Route::post('update-section-status','SectionController@updateSectionStatus');

        // Brand
        Route::get('brands','BrandController@brands');
        Route::post('update-brand-status','BrandController@updateBrandStatus');
        Route::match(['get','post'],'add-edit-brand/{id?}','BrandController@addEditBrand');
        Route::get('delete-brand/{id}','BrandController@deleteBrand');


        // Categories
        Route::get('categories','CategoryController@categories');
        Route::post('update-category-status','CategoryController@updateCategoryStatus');
        Route::match(['get','post'],'add-edit-category/{id?}','CategoryController@addEditCategory');
        Route::post('append-categories-level','CategoryController@appendCategoryLevel');
        Route::get('delete-category-image/{id}','CategoryController@deleteCategoryImage');
        Route::get('delete-category/{id}','CategoryController@deleteCategory');

        // Product
        Route::get('products','ProductsController@products');
        Route::post('update-product-status','ProductsController@updateProductStatus');
        Route::get('delete-product/{id}','ProductsController@deleteProduct');
        Route::match(['get', 'post'], 'add-edit-product/{id?}','ProductsController@addEditProduct');
        Route::get('delete-product-image/{id}','ProductsController@deleteProductImage');
        Route::get('delete-product-video/{id}','ProductsController@deleteProductVideo');

        // Attributes
        Route::match(['get', 'post'], 'add-attributes/{id}','ProductsController@addAttributes');
        Route::post('edit-attributes/{id}','ProductsController@editAttributes');
        Route::post('update-attribute-status','ProductsController@updateAttributeStatus');
        Route::get('delete-attribute/{id}','ProductsController@deleteAttribute');

        // Images
        Route::match(['get', 'post'], 'add-images/{id}', 'ProductsController@addImages');
        Route::post('update-image-status','ProductsController@updateImageStatus');
        Route::get('delete-image/{id}','ProductsController@deleteImage');

        // Banners
        Route::get('banners','BannersController@banners');
        Route::match(['get', 'post'], 'add-edit-banner/{id?}','BannersController@addeditBanner');
        Route::post('update-banner-status','BannersController@updateBannerStatus');
        Route::get('delete-banner/{id}','BannersController@deleteBanner');

    });
});

Route::namespace('Front')->group(function(){
    // Home Page Route
    Route::get('/', 'IndexController@index');

    // Listing Categories Route
    // Route::get('/{url}', 'ProductsController@listing');

    //Get Category URL
    $catUrls= Category::select('url')->where('status',1)->get()->pluck('url')->toArray();
    foreach ($catUrls as $url) {
        Route::get('/'.$url, 'ProductsController@listing');
    }

    // Product Detail Route
    Route::get('/product/{id}', 'ProductsController@detail');

    // Get Product Attribute Price
    Route::post('/get-product-price', 'ProductsController@getProductPrice');

    // Add to Cart Route
    Route::post('/add-to-cart', 'ProductsController@addtoCart');

    // Shooping Cart Route
    Route::get('/cart', 'ProductsController@cart');

    // Update Cart Item Quantity
    Route::post('/update-cart-item-qty', 'ProductsController@updateCartItemQty');

    // Delete Cart Item
    Route::post('/delete-cart-item','ProductsController@deleteCartItem');

    // Login / Register Page
    Route::get('/login-register','UsersController@loginRegister');

    // Login User
    Route::post('/login','UsersController@loginUser');

    // Register User
    Route::post('/register','UsersController@registerUser');

    // Check if Email already exists
    ROute::match(['get','post'],'/check-email','UsersController@checkEmail');

    // logout User
    Route::get('/logout', 'UsersController@logoutUser');
});
