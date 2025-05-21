<?php

use App\Http\Controllers\ArrivedController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SouvenirController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoorprizeController;
use App\Http\Controllers\BlastingController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\CustomQrController;

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
    return redirect('/dashboard');
    // return view('guest.index');   
});

Route::get('/doorprize', [DoorprizeController::class, 'index'])->name('doorprize.index');
Route::get('/doorprize/wheel', [DoorprizeController::class, 'spinWheel'])->name('doorprize.wheel');
Route::get('/doorprize/slots', [DoorprizeController::class, 'slotMachine'])->name('doorprize.slots');
Route::get('/doorprize/random', [DoorprizeController::class, 'randomPick'])->name('doorprize.random');
Route::post('/doorprize/draw', [DoorprizeController::class, 'drawWinner'])->name('doorprize.draw');
Route::get('/doorprize/winners', [DoorprizeController::class, 'getWinners'])->name('doorprize.winners');
Route::post('/doorprize/reset', [DoorprizeController::class, 'resetWinners'])->name('doorprize.reset');

// Route login
Route::get('login', [AuthController::class, "login"])->middleware('guest')->name('login');
Route::post('login-process', [AuthController::class, "loginProcess"])->middleware('guest');
Route::get('logout', [AuthController::class, "logout"]);

// For guest 
Route::controller(InvitationController::class)->group(function () {
    Route::get('/invitation/{qrcode}', 'linkGuest')->name('link-guest');
    Route::get('/download/{qrcode}', 'downloadQrCode');
    Route::get('/register', 'guestRegister');
    Route::post('/register-process', 'guestRegisterProcess');
});

Route::controller(ScanController::class)->group(function () {
    Route::get('/scan/greeting', 'greeting');
    Route::get('/scan/verify/{code}', 'scanVerify');
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

    // Route souvenir claim
    Route::controller(SouvenirController::class)->group(function () {
        Route::get('/souvenir/scan', 'index')->name('souvenir.scan');
        Route::post('/souvenir/process-claim', 'processClaim')->name('souvenir.process');
        Route::get('/souvenir/logs', 'logs')->name('souvenir.logs');
        Route::get('/souvenir/export', 'export')->name('souvenir.export');
    });

     // Route user change password
     Route::controller(UserController::class)->group(function () {
        Route::get('/user-profile', 'profile');
        Route::get('/change-password', 'changePassword');
        Route::post('/change-password-process', 'changePasswordProcess');
    });

    // RSVP Management (Admin)
    Route::controller(RsvpController::class)->group(function () {
        Route::get('/rsvp', 'index')->name('rsvp.index');
        Route::get('/rsvp/settings', 'settings')->name('rsvp.settings');
        Route::post('/rsvp/settings', 'updateSettings')->name('rsvp.updateSettings');
        Route::post('/rsvp/send-reminders', 'sendReminders')->name('rsvp.sendReminders');
    });

    // ===================================================================

    // Akses admin
    Route::middleware(['admin'])->group(function () {

        // Route Event
        Route::controller(EventController::class)->group(function () {
            Route::get('/event', 'index');
            Route::get('/event/list', 'listEvent');
            Route::post('/event/add', 'addEvent');
            Route::put('/event/update/{id}', 'updateEvent');
            Route::delete('/event/delete/{id}', 'deleteEvent');
        });

        // Setting app
        Route::controller(SettingController::class)->group(function () {
            Route::get('/setting', 'index');
            Route::put('/setting', 'update')->name('setting.settingAppUpdate');
            Route::get('/setting/rsvp', 'rsvpSettings')->name('setting.rsvpSettings');
            Route::put('/setting/rsvp-update', 'rsvpSettingsUpdate')->name('setting.rsvpSettingsUpdate');
        });

        // Route User
        Route::controller(UserController::class)->group(function () {
            Route::get('/user', 'index');
            Route::get('/user/list', 'listUser');
            Route::post('/user/add', 'addUser');
            Route::put('/user/update/{id}', 'updateUser');
            Route::delete('/user/delete/{id}', 'deleteUser');
        });

        // Route Invitation
        Route::controller(InvitationController::class)->group(function () {
            Route::get('/invitation', 'index');
            Route::get('/invitation/list', 'listInvitation');
            Route::get('/invitation/info/{id}', 'infoInvitation');
            Route::get('/invitation/export', 'exportInvitation');
            Route::post('/invitation/generate', 'generateInvitation');
            Route::post('/invitation/blast', 'blastInvitation');
            Route::delete('/invitation/delete-all', 'deleteAllInvitation');

            Route::get('/pdf/{qrcode}', 'streamPDF');
            
            // Add the missing /invite routes
            Route::get('/invite', 'index');
            Route::get('/invite/create', 'create');
            Route::post('/invite/store', 'store');
            Route::get('/invite/edit/{id}', 'edit');
            Route::put('/invite/update/{id}', 'update');
            Route::delete('/invite/delete', 'delete');
            Route::get('/invite/send-email', 'sendEmail');
        });

        // Blasting routes
        Route::controller(BlastingController::class)->group(function () {
            Route::get('/blasting', 'index')->name('blasting.index');
            Route::post('/blasting/send', 'send')->name('blasting.send');
        });

        // Email template settings
        Route::controller(SettingController::class)->group(function () {
            Route::get('/setting/email-template', 'emailTemplate')->name('setting.emailTemplate');
            Route::put('/setting/email-template-update', 'emailTemplateUpdate')->name('setting.emailTemplateUpdate');
        });
    });
});

// Guest RSVP Routes (Public)
Route::controller(RsvpController::class)->group(function () {
    Route::get('/rsvp/guest/{qrcode}', 'guestRsvpForm')->name('rsvp.guestForm');
    Route::post('/rsvp/guest/{qrcode}', 'processRsvp')->name('rsvp.process');
    Route::get('/rsvp/thank-you/{qrcode}', 'thankYou')->name('rsvp.thank-you');
});

// OLD email tracking routes - disabled in favor of mail-tracker
// Route::get('/track-email/{code}', [BlastingController::class, 'track'])->name('blasting.track');
// Route::get('/track-confirm/{code}', [BlastingController::class, 'trackConfirm'])->name('blasting.trackConfirm');

// Mail-tracker admin routes (requires admin access)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/email-analytics', function() {
        return redirect('/email-manager'); // Redirect to mail-tracker's admin panel
    })->name('email.analytics');
});
