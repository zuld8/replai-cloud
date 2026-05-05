<?php

use App\Http\Controllers\Administrator\BankController;
use App\Http\Controllers\Administrator\Blog\BlogCategoryController;
use App\Http\Controllers\Administrator\Blog\BlogController;
use App\Http\Controllers\Administrator\HomeController;
use App\Http\Controllers\Administrator\InternalSettingController;
use App\Http\Controllers\Administrator\Master\CourierController;
use App\Http\Controllers\Administrator\NotificationController;
use App\Http\Controllers\Administrator\ProfileController;
use App\Http\Controllers\Administrator\Saas\BusinessController;
use App\Http\Controllers\Administrator\Saas\MerchantCategoryController;
use App\Http\Controllers\Administrator\Saas\MerchantController;
use App\Http\Controllers\Administrator\Saas\PricingController;
use App\Http\Controllers\Administrator\Saas\StoragePricingController;
use App\Http\Controllers\Administrator\TransactionController;
use App\Http\Controllers\Administrator\Website\FooterLinkController;
use App\Http\Controllers\Administrator\Website\PageController;
use App\Http\Controllers\Auth\ProfileController as AuthProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'home'])->name('admin.index');


Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('change', [AuthProfileController::class, 'profile'])->name('admin.profile.change');
    Route::post('password', [AuthProfileController::class, 'password'])->name('admin.profile.password');
});

Route::prefix('dashboard')->group(function () {
    Route::get('analisis', [HomeController::class, 'analiss']);
    Route::get('response-ai', [HomeController::class, 'creditAiResponse']);
});

Route::prefix('banks')->group(function () {
    Route::get('/', [BankController::class, 'index'])->name('banks');
    Route::get('create', [BankController::class, 'create'])->name('banks.create');
    Route::get('update/{bank}', [BankController::class, 'update'])->name('banks.update');
    Route::post('store', [BankController::class, 'store'])->name('banks.store');
    Route::post('edit/{bank}', [BankController::class, 'edit'])->name('banks.edit');
    Route::get('delete/{bank}', [BankController::class, 'delete'])->name('banks.delete');
});

Route::prefix('couriers')->group(function () {
    Route::get('/', [CourierController::class, 'index'])->name('couriers');
    Route::post('status/{courier}', [CourierController::class, 'changeStatus']);
    Route::get('create', [CourierController::class, 'create'])->name('courier.create');
    Route::get('update/{courier}', [CourierController::class, 'update'])->name('courier.update');
    Route::post('store', [CourierController::class, 'store'])->name('courier.store');
    Route::post('edit/{courier}', [CourierController::class, 'edit'])->name('courier.edit');
    Route::get('delete/{courier}', [CourierController::class, 'delete'])->name('courier.delete');
});

Route::prefix('settings')->group(function () {

    Route::get('/', [InternalSettingController::class, 'index'])->name('admin.settings');
    Route::post('generate-api-local', [InternalSettingController::class, 'generateApiKey']);
    Route::post('change', [InternalSettingController::class, 'updateConfiguration'])->name('admin.setting.change');
    Route::get('set-lang/{code}', [InternalSettingController::class, 'setLang'])->name('admin.setlang');

    Route::prefix('general')->group(function () {
        Route::get('/', [InternalSettingController::class, 'general'])->name('general.settings');
        Route::post('update', [InternalSettingController::class, 'updateGeneral'])->name('general.settings.store');
    });

    Route::prefix('website')->group(function () {
        Route::get('/', [InternalSettingController::class, 'website'])->name('website.settings');
        Route::post('update', [InternalSettingController::class, 'updateWebsite'])->name('website.settings.store');
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notification.settings');
        Route::post('update', [NotificationController::class, 'update'])->name('notification.settings.store');
    });
});

Route::prefix('packages')->group(function () {
    Route::get('/', [PricingController::class, 'index'])->name('packages');
    Route::get('create', [PricingController::class, 'create'])->name('packages.create');
    Route::get('update/{package}', [PricingController::class, 'update'])->name('packages.update');
    Route::post('store', [PricingController::class, 'store'])->name('packages.store');
    Route::post('edit/{package}', [PricingController::class, 'edit'])->name('packages.edit');
    Route::get('delete/{package}', [PricingController::class, 'delete'])->name('packages.delete');
});

Route::prefix('package-storage')->group(function () {
    Route::get('/', [StoragePricingController::class, 'index'])->name('package.storage');
    Route::get('create', [StoragePricingController::class, 'create'])->name('package.storage.create');
    Route::get('update/{package}', [StoragePricingController::class, 'update'])->name('package.storage.update');
    Route::post('store', [StoragePricingController::class, 'store'])->name('package.storage.store');
    Route::post('edit/{package}', [StoragePricingController::class, 'edit'])->name('package.storage.edit');
    Route::get('delete/{package}', [StoragePricingController::class, 'delete'])->name('package.storage.delete');
});

Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index'])->name('transactions');
    Route::get('detail/{transaction}', [TransactionController::class, 'detail'])->name('transactions.detail');
    Route::get('rejected-payment/{payment}', [TransactionController::class, 'rejectedPayment'])->name('transactions.payment.rejected');
    Route::get('approval-payment/{transaction}', [TransactionController::class, 'approvalPayment'])->name('transactions.payment.approval');

    Route::prefix('topup')->group(function () {
        Route::get('/', [TransactionController::class, 'topUp'])->name('transaction.topup');
        Route::get('detail/{transaction}', [TransactionController::class, 'topUpDetail'])->name('transactions.topup.detail');
    });

    Route::prefix('storage')->group(function () {
        Route::get('/', [TransactionController::class, 'storage'])->name('transaction.storage');
        Route::get('detail/{transaction}', [TransactionController::class, 'storageDetail'])->name('transaction.storage.detail');
    });

    Route::prefix('mua')->group(function () {
        Route::get('/', [TransactionController::class, 'mua'])->name('transaction.mua');
        Route::get('/detail/{transaction}', [TransactionController::class, 'muaDetail'])->name('transaction.mua.detail');
    });
});


Route::prefix('merchants')->group(function () {
    Route::get('/', [MerchantController::class, 'index'])->name('merchants');
    Route::get('delete/{merchant}', [MerchantController::class, 'delete'])->name('merchant.delete');
    Route::get('detail/{merchant}', [MerchantController::class, 'detail'])->name('merchants.detail');
    Route::post('status/{merchant}', [MerchantController::class, 'changeStatus']);
    Route::get('get-data', [MerchantController::class, 'getByJquery']);
    Route::get('signint/{merchant}', [MerchantController::class, 'signIntUser'])->name('merchants.signin');
    Route::get('analisis/{merchant}', [MerchantController::class, 'merchantAnalisis']);

    Route::prefix('categories')->group(function () {
        Route::get('/', [MerchantCategoryController::class, 'index'])->name('merchant.categories');
        Route::get('create', [MerchantCategoryController::class, 'create'])->name('merchant.categories.create');
        Route::get('update/{category}', [MerchantCategoryController::class, 'update'])->name('merchant.categories.update');
        Route::post('store', [MerchantCategoryController::class, 'store'])->name('merchant.categories.store');
        Route::post('edit/{category}', [MerchantCategoryController::class, 'edit'])->name('merchant.categories.edit');
        Route::get('delete/{category}', [MerchantCategoryController::class, 'delete'])->name('merchant.categories.delete');
    });

    Route::prefix('update')->group(function () {
        Route::get('/{merchant}', [MerchantController::class, 'update'])->name('merchants.update');
        Route::post('store/{merchant}', [MerchantController::class, 'edit'])->name('merchants.edit');
    });

    Route::prefix('create')->group(function () {
        Route::get('/', [MerchantController::class, 'create'])->name('merchant.create');
        Route::post('store', [MerchantController::class, 'store'])->name('merchant.store');
    });

    Route::prefix('owner')->group(function () {
        Route::get('/{merchant}', [MerchantController::class, 'owner'])->name('merchants.owner');
        Route::post('store/{merchant}', [MerchantController::class, 'updateOwner'])->name('merchants.owner.edit');
        Route::post('password/{merchant}', [MerchantController::class, 'password'])->name('merchants.owner.password');
    });
});

Route::prefix('business')->group(function () {
    Route::get('/', [BusinessController::class, 'index'])->name('business');
    Route::get('detail/{business}', [BusinessController::class, 'detail'])->name('business.detail');
    Route::get('delete/{business}', [BusinessController::class, 'deleteBusiness'])->name('business.delete');
    Route::get('analytic/{business}', [BusinessController::class, 'interactionAnalysis']);
});

Route::prefix('web-app')->group(function () {


    Route::prefix('pages')->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('pages');
        Route::post("create", [PageController::class, 'store'])->name('pages.store');
        Route::post('edit/{page}', [PageController::class, 'edit'])->name('pages.edit');
        Route::get('update/{page}', [PageController::class, 'update'])->name('pages.update');
        Route::get('delete/{page}', [PageController::class, 'delete'])->name('pages.delete');

        Route::prefix('components')->group(function () {
            Route::post('upload-assets', [PageController::class, 'uploadAsset'])->name('pages.upload_asset');
            Route::get('templates/{page}', [PageController::class, 'getComponentTemplate'])->name('pages.components');
        });
    });

    Route::prefix('blogs')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('blogs');
        Route::get('create', [BlogController::class, 'create'])->name('blogs.create');
        Route::get('update/{blog}', [BlogController::class, 'update'])->name('blogs.update');
        Route::post('store', [BlogController::class, 'store'])->name('blogs.store');
        Route::post('edit/{blog}', [BlogController::class, 'edit'])->name('blogs.edit');
        Route::get('delete/{blog}', [BlogController::class, 'delete'])->name('blogs.delete');
    });

    Route::prefix('links')->group(function () {
        Route::get('/', [FooterLinkController::class, 'index'])->name('links');
        Route::get('create', [FooterLinkController::class, 'create'])->name('links.create');
        Route::get('update/{link}', [FooterLinkController::class, 'update'])->name('links.update');
        Route::post('store', [FooterLinkController::class, 'store'])->name('links.store');
        Route::post('edit/{link}', [FooterLinkController::class, 'edit'])->name('links.edit');
        Route::get('delete/{link}', [FooterLinkController::class, 'delete'])->name('links.delete');
    });

    Route::prefix('blog-categories')->group(function () {
        Route::get('/', [BlogCategoryController::class, 'index'])->name('blog.categories');
        Route::get('create', [BlogCategoryController::class, 'create'])->name('blog.categories.create');
        Route::get('update/{category}', [BlogCategoryController::class, 'update'])->name('blog.categories.update');
        Route::post('store', [BlogCategoryController::class, 'store'])->name('blog.categories.store');
        Route::post('edit/{category}', [BlogCategoryController::class, 'edit'])->name('blog.categories.edit');
        Route::get('delete/{category}', [BlogCategoryController::class, 'delete'])->name('blog.categories.delete');
    });
});

// Affiliate Management
Route::prefix('affiliate')->group(function () {
    Route::get('/', [\App\Http\Controllers\Administrator\AffiliateAdminController::class, 'index'])->name('admin.affiliate');
    Route::get('withdrawals', [\App\Http\Controllers\Administrator\AffiliateAdminController::class, 'withdrawals'])->name('admin.affiliate.withdrawals');
    Route::post('withdraw/{withdrawal}/approve', [\App\Http\Controllers\Administrator\AffiliateAdminController::class, 'approveWithdrawal'])->name('admin.affiliate.withdraw.approve');
    Route::post('withdraw/{withdrawal}/reject', [\App\Http\Controllers\Administrator\AffiliateAdminController::class, 'rejectWithdrawal'])->name('admin.affiliate.withdraw.reject');
    Route::get('{affiliate}/ban', [\App\Http\Controllers\Administrator\AffiliateAdminController::class, 'banAffiliate'])->name('admin.affiliate.ban');
    Route::get('{affiliate}/unban', [\App\Http\Controllers\Administrator\AffiliateAdminController::class, 'unbanAffiliate'])->name('admin.affiliate.unban');
});
