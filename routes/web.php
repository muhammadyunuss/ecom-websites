<?php

use Illuminate\Support\Facades\Route;
use App\Category;
use App\Http\Controllers\Front\PayumoneyController;
use App\Http\Controllers\Front\UsersController;

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

        // Coupon
        Route::get('coupons','CouponsController@coupons');
        Route::post('update-coupon-status', 'CouponsController@updateCouponsStatus');
        Route::match(['get', 'post'], 'add-edit-coupon/{id?}', 'CouponsController@addEditCoupon');
        Route::get('delete-coupon/{id}','CouponsController@deleteCoupon');

        // Orders
        Route::get('orders','OrdersController@orders');
        Route::get('orders/{id}','OrdersController@orderDetails');
        Route::post('update-order-status','OrdersController@updateOrderStatus');
        Route::get('view-order-invoice/{id}','OrdersController@viewOrderInvoice');
        Route::get('print-pdf-invoice/{id}','OrdersController@printPDFInvoice');

        // Shipping Charges
        Route::get('view-shipping-charges','ShippingController@viewShippingCharges');
        Route::match(['get', 'post'], 'edit-shipping-charges/{id}', 'ShippingController@editShippingCharges');
        Route::post('update-shipping-status','ShippingController@updateShippingStatus');

        // User
        Route::get('users', 'UsersController@users');
        Route::post('update-user-status','UsersController@updateUserStatus');


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
    Route::get('/login-register',['as' => 'login','uses' => 'UsersController@loginRegister']);

    // Login User
    Route::post('/login','UsersController@loginUser');

    // Register User
    Route::post('/register','UsersController@registerUser')->name('registerUser');

    // Check if Email already exists
    Route::match(['get','post'],'/check-email','UsersController@checkEmail');

    // logout User
    Route::get('/logout', 'UsersController@logoutUser');

    // Confirm Account
    Route::match(['get', 'post'], '/confirm/{code}', 'UsersController@confirmAccount');

    // Forget Password
    Route::match(['get', 'post'], '/forgot-password', 'UsersController@forgotPassword');

    // Payumoney Transaction Status API
    Route::get('/payumoney/verify/{id?}','PayumoneyController@payumoneyVerify');

    // Check Delivery Pincode
    Route::post('/check-pincode','ProductsController@checkPincode');

    Route::middleware(['auth'])->group(function () {

        // User Account
        Route::match(['get', 'post'], '/account', 'UsersController@account');

        // Users Orders
        Route::get('/orders','OrdersController@orders');

        // User Order Details
        Route::get('/orders/{id}','OrdersController@orderDetails');

        // Check User Cuurent Password
        Route::post('check-user-pwd', 'UsersController@chkUserPassword');

        // Update User Current Password
        Route::post('/update-user-pwd', 'UsersController@updateUserPassword');

        // Apply Coupon
        Route::post('/apply-coupon', 'ProductsController@applyCoupon');

        // Checkout
        Route::match(['get', 'post'], '/check-out', 'ProductsController@checkout');

        // Add/Edit Delivery Address
        Route::match(['get', 'post'], '/add-edit-delivery-address/{id?}', 'ProductsController@addEditDeliveryAddress');
        // Delete Delivery Address
        Route::get('/delete-delivery-address/{id}', 'ProductsController@deleteDeliveryAddress');

        // Thanks
        Route::get('/thanks', 'ProductsController@thanks');

        // Paypal
        Route::get('/paypal','PaypalController@paypal');
        // Paypal Success
        Route::get('/paypal/success','PaypalController@success');
        // Paypal Fail
        Route::get('/paypal/fail','PaypalController@fail');
        // Paypal IPN
        Route::any('/paypal/ipn','PaypalController@ipn');

        // Payumoney
        Route::get('/payumoney','PayumoneyController@payumoney');
        // Payumoney Response
        Route::get('/payumoney/response', 'PayumoneyController@payumoneyResponse');
        // Payumoney success
        Route::get('/payumoney/success','PayumoneyController@success');
        // Payumoney Fail
        Route::get('/payumoney/fail','PayumoneyController@fail');

    });

});
