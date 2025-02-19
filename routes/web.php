<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\ReservationController;

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

Route::get('/', [AuthController::class, 'index'])->name('login');


Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/get-instructors', [InstructorController::class, 'get_instructors']);

Route::middleware(['auth'])->group(function () {
    // Logout
    Route::get('/logout', [AuthController::class, 'logout']);

    // Auth users
    Route::get('/dashboard', [AuthController::class, 'dashboard']);

    // Admin views
    Route::get('/groups', [AdminController::class, 'groups']);
    Route::get('/instructors', [AdminController::class, 'instructors']);
    Route::get('/transactions', [AdminController::class, 'transactions']);

    // Schedule
    Route::get('/calendar', [ScheduleController::class, 'index']);
    Route::post('/add_schedule', [ScheduleController::class, 'store'])->name('schedule.store');

    // Reservation
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reserve', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reserve/select-group', [ReservationController::class, 'storeGroup'])->name('reservation.storeGroup');
    Route::get('/get-all-groups', [ReservationController::class, 'getGroups']);
    Route::post('/reserve-group', [ReservationController::class, 'store'])->name('reservation.store');
});
