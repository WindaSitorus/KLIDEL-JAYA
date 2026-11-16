
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApotekerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ObatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =====================================================
// AUTHENTICATION ROUTES
// =====================================================

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::get('/login/dokter', [AuthController::class, 'showDoctorLoginForm'])
    ->name('login.doctor');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])
    ->name('quick-login');

// =====================================================
// PATIENT / USER ROUTES
// =====================================================

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/appointment-status', [HomeController::class, 'getAppointmentStatus'])
    ->name('appointment.status');

Route::post('/booking', [HomeController::class, 'storeBooking'])
    ->name('booking.store');

Route::post('/payment', [HomeController::class, 'processPayment'])
    ->name('payment.process');

Route::get('/calendar', [HomeController::class, 'calendar'])
    ->name('calendar');

Route::get('/rekam-medis', [HomeController::class, 'medicalRecords'])
    ->name('rekam-medis');

// =====================================================
// DOCTOR ROUTES
// =====================================================

Route::middleware(['doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {

        Route::get('/', [DoctorController::class, 'index'])
            ->name('dashboard');

        Route::post('/soap/{queueId}', [DoctorController::class, 'saveSoap'])
            ->name('saveSoap');

        Route::get('/obat/search', [DoctorController::class, 'searchObat'])
            ->name('obat.search');
    });

// =====================================================
// ADMIN ROUTES
// Dilindungi oleh AdminMiddleware
// =====================================================

Route::middleware(['admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/calendar', [AdminController::class, 'calendar'])->name('calendar');
        Route::get('/export-csv', [AdminController::class, 'exportCsv'])->name('exportCsv');
        Route::post('/transaction', [AdminController::class, 'storeTransaction'])->name('storeTransaction');
    });

// =====================================================
// APOTEKER ROUTES
// Fokus: MANAJEMEN OBAT DAN RESEP
// Dilindungi oleh ApotekerMiddleware
// =====================================================

Route::middleware(['apoteker'])
    ->prefix('apoteker')
    ->name('apoteker.')
    ->group(function () {
        Route::get('/', [ApotekerController::class, 'index'])->name('dashboard');
        Route::post('/tambah-obat', [ApotekerController::class, 'storeObat'])->name('tambahObat');
        Route::post('/update-stok', [ApotekerController::class, 'updateStok'])->name('updateStok');
        Route::post('/selesaikan-opname', [ApotekerController::class, 'selesaikanOpname'])->name('selesaikanOpname');
    });

// =====================================================
// OBAT MANAGEMENT ROUTES
// Harus dibatasi khusus untuk pengguna yang berwenang
// melalui middleware obat.manager
// =====================================================

Route::resource('obat', ObatController::class)
    ->middleware('obat.manager');
