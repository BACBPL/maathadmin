<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\CategoryController;
use App\Http\Controllers\Panel\SubCategoryController;
use App\Http\Controllers\Panel\VendorController;
use App\Http\Controllers\Panel\UserWalletController;
use App\Http\Controllers\Panel\VendorServiceController;
use App\Http\Controllers\Panel\VendorAreaController;
use App\Http\Controllers\Panel\VendorWiseServiceController;
use App\Http\Controllers\Panel\VendorAreaListController;
use App\Http\Controllers\Panel\VendorServicePriceController;
use App\Http\Controllers\Panel\VendorPriceReviewController;
use App\Http\Controllers\Panel\VendorProductController;
use App\Http\Controllers\Panel\EmployeeController;
use App\Http\Controllers\Panel\OrderController;

// Redirect “/” to the login page if guest, or straight to dashboard if already authenticated
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('panel.dashboard')
        : redirect()->route('login');
});

// Fortify’s login, register, etc. are already registered for you…

// Dashboard (panel) routes without auth middleware
Route::name('panel.')->group(function () {

    Route::view('dashboard', 'pages.panel.dashboard')->name('dashboard');

    // Categories
    Route::get('category/create', [CategoryController::class, 'create'])->name('create_cat');
    Route::post('category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('category/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('category/{category}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');

    // Subcategories
    Route::get('subcategory/create', [SubCategoryController::class, 'create'])->name('subcategory.create');
    Route::post('subcategory/create', [SubCategoryController::class, 'store'])->name('subcategory.store');
    Route::get('subcategory/{subcategory}/edit', [SubCategoryController::class, 'edit'])->name('subcategory.edit');
    Route::put('subcategory/{subcategory}', [SubCategoryController::class, 'update'])->name('subcategory.update');
    Route::delete('subcategory/{subcategory}', [SubCategoryController::class, 'destroy'])->name('subcategory.destroy');

    // Vendor creation and documents
    Route::get('vendor/create', [VendorController::class, 'createBasic'])->name('vendor.create');
    Route::post('vendor/create', [VendorController::class, 'storeBasic'])->name('vendor.store');
    Route::get('vendor/{vendor}/docs', [VendorController::class, 'createDocs'])->name('vendor.docs.create');
    Route::post('vendor/{vendor}/docs', [VendorController::class, 'storeDocs'])->name('vendor.docs.store');

    // Wallet screens
    Route::get('user/balance', [UserWalletController::class, 'index'])->name('user.balance');
    Route::post('vendor/{vendor}/wallet/add', [UserWalletController::class, 'store'])->name('vendor.wallet.add');

    // Vendor services
    Route::get('vendor/services', [VendorServiceController::class, 'edit'])->name('vendor.services.edit');
    Route::post('vendor/services', [VendorServiceController::class, 'update'])->name('vendor.services.update');

    Route::get('vendor/services/area',  [VendorAreaController::class, 'edit'])
        ->name('vendor.services.area');

    Route::post('vendor/services/area', [VendorAreaController::class, 'update'])
        ->name('vendor.services.area.save');
     Route::get('/vendor/services/vendor-wise', [VendorWiseServiceController::class, 'index'])
        ->name('vendor.service'); 
     Route::get('/vendor/services/area-wise', [VendorAreaListController::class, 'index'])
        ->name('vendor.area');

    Route::patch('/vendor/services/area-wise/{area}/approve', [VendorAreaListController::class, 'approve'])
        ->name('vendor.area.approve');      

    Route::get('/vendor/services/price', [VendorServicePriceController::class, 'index'])
        ->name('vendor.services.price');
    Route::post('/vendor/services/price', [VendorServicePriceController::class, 'store'])
        ->name('vendor.services.price.save');    

    Route::get('/vendor/price', [VendorPriceReviewController::class, 'index'])
        ->name('vendor.price');

    Route::post('/vendor/price/approve/{price}', [VendorPriceReviewController::class, 'approve'])
        ->name('vendor.price.approve');    

    Route::get('/product/add', [VendorProductController::class, 'create'])
            ->name('vendor.product.add'); // matches your nav link

        Route::post('/product/store', [VendorProductController::class, 'store'])
            ->name('vendor.product.store');    

    Route::get('/products',                   [VendorProductController::class,'index'])->name('vendor.product.edit');       // nav link points here
    Route::get('/products/{product}/edit',    [VendorProductController::class,'edit'])->name('vendor.product.edit.form');
    Route::put('/products/{product}',         [VendorProductController::class,'update'])->name('vendor.product.update');      
    
    Route::get('/products/vendors', [VendorProductController::class, 'vendorIndex'])
            ->name('vendor.products');

    Route::get('employee/create', [EmployeeController::class, 'createBasic'])->name('employee.create');
    Route::post('employee/create', [EmployeeController::class, 'storeBasic'])->name('employee.store');
    Route::get('employee/{employee}/docs', [EmployeeController::class, 'createDocs'])->name('employee.docs.create');
    Route::post('vendor/{vendor}/docs', [EmployeeController::class, 'storeDocs'])->name('vendor.docs.store');   
    
    Route::get('check/orders', [OrderController::class, 'orders'])->name('product.order');
    Route::get('orders/{id}', [OrderController::class, 'orderDetails'])->name('orders.show');
    
    
});
