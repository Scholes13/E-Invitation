<?php

use App\Http\Controllers\ArrivedController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoorprizeController;
use App\Http\Controllers\BlastingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/register');
    // return view('guest.index');
});

Route::get('/doorprize', [DoorprizeController::class, 'index'])->name('doorprize.index');
Route::post('/doorprize/draw', [DoorprizeController::class, 'drawWinner'])->name('doorprize.draw');

// Route login
Route::get('login', [AuthController::class, "login"])->middleware('guest')->name('login');
Route::post('login-process', [AuthController::class, "loginProcess"])->middleware('guest');
Route::get('logout', [AuthController::class, "logout"]);

// For guest 
Route::controller(InvitationController::class)->group(function () {
    Route::get('/invitation/{qrcode}', 'linkGuest');
    Route::get('/download/{qrcode}', 'downloadQrCode');
    Route::get('/register', 'guestRegister');
    Route::post('/register-process', 'guestRegisterProcess');
});

Route::controller(ScanController::class)->group(function () {
    Route::get('/scan/greeting', 'greeting');
});

Route::middleware(['auth'])->group(function () {

    // Akses resepsionis or admin is login
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
    });

    Route::controller(ArrivedController::class)->group(function () {
        Route::get('/arrived-manually', 'index');
        Route::put('/arrived-manually/process-scan', 'processScan');

        Route::get('/arrival-log', 'arrivalLog');
        Route::get('/arrival-log/export', 'arrivalLogExport');
        Route::get('/arrival-log/{id}', 'arrivalLogDetail');

        // Tambahkan rute ini untuk deleteAllLogs
        Route::delete('/arrival-log/delete-all', 'deleteAllLogs');
    });

    // Route scan in and out
    Route::controller(ScanController::class)->group(function () {
        Route::get('/scan/in', 'scanIn');
        Route::post('/scan/in-process', 'scanInProcess');
        Route::get('/scan/out', 'scanOut');
        Route::post('/scan/out-process', 'scanOutProcess');
    });

     // Route user change password
     Route::controller(UserController::class)->group(function () {
        Route::get('/user-profile', 'profile');
        Route::get('/change-password', 'changePassword');
        Route::post('/change-password-process', 'changePasswordProcess');
    });

    // ===================================================================

    // Akses admin
    Route::middleware(['admin'])->group(function () {
        Route::controller(GuestController::class)->group(function () {
            Route::get('/guest', 'index');
            Route::get('/guest/create', 'create');
            Route::post('/guest/store', 'store');
            Route::get('/guest/edit/{id}', 'edit');
            Route::put('/guest/update/{id}', 'update');
            Route::delete('/guest/delete', 'delete');
        });

        // Route invite
        Route::controller(InvitationController::class)->group(function () {
            Route::get('/invite/get-guest', 'getGuest');
            Route::get('/invite', 'index');
            Route::get('/invite/create', 'create');
            Route::post('/invite/store', 'store');
            Route::get('/invite/edit/{id}', 'edit');
            Route::put('/invite/update/{id}', 'update');
            Route::delete('/invite/delete', 'delete');
            Route::get('/invite/send-email', 'sendEmail');
            Route::get('/invite/send-whatsapp', 'sendWhatsapp');
            Route::get('/invite/preview-email/{qrcode}', 'linkGuestEmail');
        });

        // Route arrived and arrival log
        Route::controller(EventController::class)->group(function () {
            Route::get('/event', 'index');
            Route::put('/event/set', 'setEvent');
            // link for user
            Route::get('/registered/set', 'setEvent');

        });

        // Route setting app
        Route::controller(SettingController::class)->group(function () {
            Route::get('setting', 'index')->name('setting.setting_app');
            Route::put('setting/update', 'settingAppUpdate')->name('setting.settingAppUpdate');
            Route::get('setting/email-template', 'emailTemplate')->name('setting.emailTemplate');
            Route::put('setting/email-template/update', 'emailTemplateUpdate')->name('setting.emailTemplateUpdate');
        });

        // Route user
        Route::controller(UserController::class)->group(function () {
            Route::get('/user', 'index');
            Route::get('/user/create', 'create');
            Route::post('/user/store', 'store');
            Route::get('/user/edit/{id}', 'edit');
            Route::put('/user/update/{id}', 'update');
            Route::delete('/user/delete', 'delete');
        });
    });

    // Route blasting
    Route::controller(BlastingController::class)->group(function () {
        Route::get('/blasting', 'index')->name('blasting.index');
        Route::post('/blasting/send', 'send')->name('blasting.send');
    })->middleware('admin');
});
