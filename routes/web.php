<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewReactionController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Termwind\Components\Hr;
use App\Http\Controllers\PaymongoController;
use App\Http\Controllers\PaymongoWebhookController;
use App\Http\Controllers\CheckoutController;
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


Route::get('/paymongo', [PaymongoController::class, 'index'])->name('paymongo.index');
Route::post('/paymongo/create-payment', [PaymongoController::class, 'createPayment'])->name('paymongo.createPayment');

// Webhook (PayMongo will POST here)
// PayMongo will POST here (webhook)
Route::post('/paymongo/webhook', [PaymongoWebhookController::class, 'handlePaymongoWebhook'])->name('paymongo.webhook');


Auth::routes(['verify' => true]);

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop',[ShopController::class,'index'])->name('shop.index');
Route::get('/shop/{product_slug}',[ShopController::class,'product_details'])->name('shop.product.details');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('product.details');


Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart/add',[CartController::class,'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}',[CartController::class,'increase_cart_quantity'])->name('cart.qty.increase');
Route::put('/cart/decrease-quantity/{rowId}',[CartController::class,'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}',[CartController::class,'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear',[CartController::class,'empty_cart'])->name('cart.empty');
Route::put('/cart/update/{rowId}', [CartController::class, 'update'])->name('cart.update');

Route::post('/cart/apply-coupon',[CartController::class,'apply_coupon_code'])->name('cart.coupon.apply');
Route::delete('/cart/remove-coupon',[CartController::class,'remove_coupon_code'])->name('cart.coupon.remove');


Route::post('wishlist/add',[WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::get('wishlist',[WishlistController::class,'index'])->name('wishlist.index');
Route::delete('/wishlist/item/remove/{rowId}',[WishlistController::class,'remove_item'])->name('wishlist.item.remove');
Route::delete('/wishlist/clear',[WishlistController::class,'empty_wishlist'])->name('wishlist.items.clear');
Route::post('/wishlist/move-to-cart/{rowId}',[WishlistController::class,'move_to_cart'])->name('wishlist.move.to.cart');

Route::get('/checkout',[CartController::class,'checkout'])->name('cart.checkout');
Route::post('/place-an-order',[CartController::class,'place_an_order'])->name('cart.place.an.order');
Route::get('/transaction/{transaction}/status', [CartController::class, 'transactionStatus'])->name('transaction.status');

// DEV: debug route to preview PayMongo payload without sending to PayMongo
Route::get('/debug/paymongo-payload/{userId}', [App\Http\Controllers\CartController::class, 'debugPaymongoPayload'])
    ->name('debug.paymongo.payload');
Route::get('/order-confirmation',[CartController::class,'order_confirmation'])->name('cart.order.confirmation');
// Public QR scan endpoint for buyers to confirm receipt
Route::get('/order/verify/{token}', [CartController::class, 'scanQr'])->name('order.qr.scan');

Route::get('/contact-us',[HomeController::class,'contact'])->name('home.contact');
Route::post('/contact/store',[HomeController::class,'contact_store'])->name('home.contact.store');

Route::get('/search',[HomeController::class,'search'])->name('home.search');
Route::get('/search', [App\Http\Controllers\ShopController::class, 'search'])->name('shop.search');



Route::middleware(['auth','verified'])->group(function(){
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/account-order/{order_id}/details', [UserController::class, 'order_details'])->name('user.order.details');
    Route::post('/order/{order}/settle', [CheckoutController::class, 'settleOrder'])->name('order.settle');
    Route::put('/account-order/cancel-order',[UserController::class,'order_canceled'])->name('user.order.cancel');
    Route::post('/update-profile-photo', [UserController::class, 'updateProfilePhoto'])
    ->name('user.updateProfilePhoto')
    ->middleware('auth');
});

Route::middleware(['auth', AuthAdmin::class])->group(function(){
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/brands', [AdminController::class,'brands'])->name('admin.brands');
    Route::get('/admin/brand/add',[AdminController::class, 'add_brand'])->name('admin.brand.add');
    Route::post('/admin/brand/store',[AdminController::class,'brand_store'])->name('admin.brand.store');
    Route::get('/admin/brand/edit/{id}',[AdminController::class, 'brand_edit'])->name('admin.brand.edit');
    Route::put('/admin/brand/update/{id}',[AdminController::class,'brand_update'])->name('admin.brand.update');
    Route::delete('/admin/brand/{id}/delete', [AdminController::class, 'brand_delete'])->name('admin.brand.delete');

    Route::get('/admin/categories',[AdminController::class,'categories'])->name('admin.categories');
    Route::get('/admin/category/add',[AdminController::class,'category_add'])->name('admin.category.add');
    Route::post('/admin/category/store',[AdminController::class,'category_store'])->name('admin.category.store');
    Route::get('/admin/category/{id}/edit', [AdminController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('/admin/category/update', [AdminController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/admin/category/{id}/delete', [AdminController::class, 'category_delete'])->name('admin.category.delete');

    Route::get('/admin/products',[AdminController::class,'products'])->name('admin.products');
    Route::get('/admin/product/add',[AdminController::class,'product_add'])->name('admin.product.add');
    Route::post('/admin/product/store',[AdminController::class,'product_store'])->name('admin.product.store');
    Route::get('/admin/product/{id}/edit',[AdminController::class,'product_edit'])->name('admin.product.edit');
    Route::put('/admin/product/update', [AdminController::class, 'product_update'])->name('admin.product.update');
    Route::delete('/admin/product/{id}/delete',[AdminController::class,'product_delete'])->name('admin.product.delete');

    Route::get('/admin/coupons',[AdminController::class,'coupons'])->name('admin.coupons');
    Route::get('/admin/coupon/add',[AdminController::class,'coupon_add'])->name('admin.coupon.add');
    Route::post('/admin/coupon/store',[AdminController::class,'coupon_store'])->name('admin.coupon.store');
    Route::get('/admin/coupon/{id}/edit',[AdminController::class,'coupon_edit'])->name('admin.coupon.edit');
    Route::put('/admin/coupon/update',[AdminController::class,'coupon_update'])->name('admin.coupon.update');
    Route::delete('/admin/coupon/{id}/delete',[AdminController::class,'coupon_delete'])->name('admin.coupon.delete');

    Route::get('/admin/orders',[AdminController::class,'orders'])->name('admin.orders');
    Route::get('/admin/order/{order_id}/details',[AdminController::class,'order_details'])->name('admin.order.details');
    Route::get('/admin/order/{order}/qr.png', [AdminController::class, 'qrImage'])->name('admin.order.qr.image');
    Route::get('/admin/order/{order}/pack-slip.pdf', [AdminController::class, 'packSlip'])->name('admin.order.packslip');
    Route::put('/admin/order/update-statues',[AdminController::class,'update_order_status'])->name('admin.order.status.update');

    Route::get('/admin/slides', [AdminController::class,'slides'])->name('admin.slides');
    Route::get('/admin/slide/add', [AdminController::class,'slide_add'])->name('admin.slide.add');
    Route::post('/admin/slide/store',[AdminController::class,'slide_store'])->name('admin.slide.store');
    Route::get('/admin/slide/{id}/edit',[AdminController::class, 'slide_edit'])->name('admin.slide.edit');
    Route::put('/admin/slide/update',[AdminController::class,'slide_update'])->name('admin.slide.update');
    Route::delete('/admin/slide/{id}/delete',[AdminController::class,'slide_delete'])->name('admin.slide.delete');

    Route::get('/admin/contact',[AdminController::class, 'contacts'])->name('admin.contacts');
    Route::delete('/admin/contact/{id}/delete',[AdminController::class,'contact_delete'])->name('admin.contact.delete');

    Route::get('/admin/search',[AdminController::class,'search'])->name('admin.search');

    Route::get('/admin/order', [AdminController::class,'order_tracking'])->name('admin.order');

    Route::post('/review', [ReviewController::class, 'store'])->name('review.store');

});

Route::post('/review', [ReviewController::class, 'store'])->name('review.store');
Route::post('/review/{product_id}', [ReviewController::class, 'store'])->name('review.store');
Route::get('/product/{id}/reviews/filter', [ReviewController::class, 'filter'])
    ->name('reviews.filter');

Route::post('/reviews/{review}/react', [ReviewReactionController::class, 'react'])->name('reviews.react')->middleware('auth');

Route::middleware(['auth'])->prefix('reviews')->name('reviews.')->group(function () {
    Route::get('{review}/edit', [ReviewController::class, 'edit'])->name('edit');
    Route::put('{review}', [ReviewController::class, 'update'])->name('update');
    Route::delete('{review}', [ReviewController::class, 'destroy'])->name('delete');
});
