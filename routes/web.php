<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// ក្រុមទី ១៖ គ្រប់គ្នាដែលបាន Login រួចអាចប្រើប្រាស់បាន (Admin, Staff, Cashier)
// ==========================================
Route::middleware(['auth'])->group(function () {

    // ទំព័រ Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ប្រព័ន្ធលក់ទំនិញ (POS)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // គ្រប់គ្រងព័ត៌មានអតិថិជន (Cashier ត្រូវការចុះឈ្មោះអតិថិជនពេលលក់ទំនិញ)
    Route::resource('customers', CustomerController::class);

    // ប្រវត្តិរូបផ្ទាល់ខ្លួន (Profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

// ==========================================
// ក្រុមទី ២៖ សម្រាប់តែ Admin និង Staff ប៉ុណ្ណោះ (Cashier មិនអាចចូលបានទេ)
// ==========================================
Route::middleware(['auth', 'role:staff'])->group(function () {

    // គ្រប់គ្រងទំនិញ និងប្រភេទផលិតផល
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);

    // គ្រប់គ្រងការបញ្ជាទិញ (Orders)
    // សម្គាល់៖ Route សម្រាប់ Export ត្រូវតែនៅពីលើ Resource Route ជានិច្ច
    Route::get('orders/export/excel', [OrderController::class, 'exportExcel'])
        ->name('orders.export.excel');

    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])
        ->name('orders.invoice');

    Route::resource('orders', OrderController::class);

});

// ==========================================
// ក្រុមទី ៣៖ សម្រាប់តែ Admin តែម្នាក់គត់ (Staff និង Cashier មិនអាចចូលបានទេ)
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // ផ្នែករបាយការណ៍លក់ និងការទាញយកទិន្នន័យ (Reports & Exports)
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/pdf', [ExportController::class, 'pdf'])
        ->name('reports.pdf');

    Route::get('/reports/excel', [ExportController::class, 'excel'])
        ->name('reports.excel');

    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])
        ->name('reports.export.excel');

     Route::resource('users', UserController::class);

    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups/create', [BackupController::class, 'create'])->name('backups.create');
    Route::get('/backups/download/{filename}', [BackupController::class, 'download'])->name('backups.download');
    Route::post('/backups/restore/{filename}', [BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('/backups/delete/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');

    // Routes សម្រាប់គ្រប់គ្រងការកំណត់ព័ត៌មានហាង
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
Route::get('/install-system', function () {
    // ១. បង្កើតតារាង Database និងបញ្ចូលគណនីគំរូទាំងអស់
    Artisan::call('migrate:fresh --seed --force');

    // ២. បង្កើតតំណភ្ជាប់រូបភាពផលិតផល
    Artisan::call('storage:link');

    return 'ការដំឡើងប្រព័ន្ធ POS ទទួលបានជោគជ័យ!';
});
require __DIR__.'/auth.php';
