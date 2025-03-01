<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PanelistController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TransactionController;

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
// Route::get('/get-instructors', [InstructorController::class, 'get_instructors']);

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
    Route::get('/schedules', [ScheduleController::class, 'getSchedules']);

    // Reservation
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reserve', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reserve/select-group', [ReservationController::class, 'storeGroup'])->name('reservation.storeGroup');
    Route::get('/get-all-groups', [ReservationController::class, 'getGroups']);
    Route::post('/reserve-group', [ReservationController::class, 'store'])->name('reservation.store');
    Route::delete('/delete_reservation/{reservation_id}', [ReservationController::class, 'destroy'])->name('reservation.destroy');

    // Transaction
    Route::get('/transactions', [TransactionController::class, 'index']);

    // Panelists
    Route::get('/panelists', [PanelistController::class, 'index']);
    Route::get('/add_panelist', [PanelistController::class, 'create'])->name('panelist.create');
    Route::post('/add_panelist', [PanelistController::class, 'store'])->name('panelist.store');
    Route::get('/assign_panelist/{id}', [PanelistController::class, 'showForm'])->name('assign_panelist.form');
    Route::post('/assign_panelists', [PanelistController::class, 'assignPanelists'])->name('assign_panelist.store');
    Route::get('/update_panelist/{id}', [PanelistController::class, 'updateForm']);
    Route::post('/update_panelist/{id}', [PanelistController::class, 'updatePanelist'])->name('panelist.update');
    Route::delete('/delete_panelist/{id}', [PanelistController::class, 'destroy'])->name('panelist.destroy');

    // Group
    Route::get('/update_group/{id}', [GroupController::class, 'updateGroupForm']);
    Route::post('/update_group/{id}', [GroupController::class, 'updateGroup'])->name('group.update');
    Route::delete('/delete_group/{id}', [GroupController::class, 'deleteGroup'])->name('groups.delete');

    // Instructor
    Route::get('/update_instructor/{id}', [InstructorController::class, 'updateInstructorForm']);
    Route::post('/update_instructor/{id}', [InstructorController::class, 'updateInstructor'])->name('instructor.update');
    Route::delete('/delete_instructor/{id}', [InstructorController::class, 'deleteInstructor'])->name('instructor.delete');
});
