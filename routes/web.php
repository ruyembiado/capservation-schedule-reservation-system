<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CapstoneController;
use App\Http\Controllers\PanelistController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\NotificationController;

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
    Route::get('/smart-scheduler/{group_id}', [AdminController::class, 'SmartScheduler'])->name('SmartScheduler');
    Route::post('/smart-scheduler/{group_id}', [AdminController::class, 'runSmartScheduler'])->name('SmartScheduler.run');
    Route::post('/create-panelist-schedule', [AdminController::class, 'CreatePanelistSchedule'])->name('assign.panelist.schedule');
    Route::post('/assign-panelists-scheduler', [AdminController::class,
    'assignedPanelistsScheduler'])->name('assignedPanelistsScheduler');
    Route::get('/settings', [AdminController::class, 'settings']);
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

    // Schedule
    Route::get('/calendar', [ScheduleController::class, 'index']);
    Route::post('/add-schedule', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::get('/schedules', [ScheduleController::class, 'getSchedules']);

    // Reservation
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservation/{id}/{action}/{notif_id}', [ReservationController::class, 'show'])->name('reservation.show');
    Route::get('/reserve', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reserve/select-group', [ReservationController::class, 'storeGroup'])->name('reservation.storeGroup');
    Route::get('/get-all-groups', [ReservationController::class, 'getGroups']);
    Route::post('/reserve-group', [ReservationController::class, 'store'])->name('reservation.store');
    Route::delete('/delete-reservation/{reservation_id}', [ReservationController::class, 'destroy'])->name('reservation.destroy');
    Route::post('/reservation/reschedule/{id}', [ScheduleController::class, 'update'])->name('schedule.reschedule');
    Route::get('/awaiting-reservations', [ReservationController::class, 'awaiting_reservations'])->name('awaiting_reservations.index');
    Route::get('/payment-confirmation/{reservation_id}', [ReservationController::class,
    'payment'])->name('payment_confirm');

    // Transaction
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/update-transaction/{id}', [TransactionController::class, 'update'])->name('transaction.update');
    Route::post('/transaction/upload-proof', [TransactionController::class, 'uploadProof'])->name('transaction.upload_proof');

    // Panelists
    Route::get('/panelists', [PanelistController::class, 'index']);
    Route::get('/add-panelist', [PanelistController::class, 'create'])->name('panelist.create');
    Route::post('/add-panelist', [PanelistController::class, 'store'])->name('panelist.store');
    Route::get('/assign-panelist/{id}', [PanelistController::class, 'showForm'])->name('assign_panelist.form');
    Route::post('/assign-panelists', [PanelistController::class, 'assignPanelists'])->name('assign_panelist.store');
    Route::get('/update-panelist/{id}', [PanelistController::class, 'updateForm']);
    Route::post('/update-panelist/{id}', [PanelistController::class, 'updatePanelist'])->name('panelist.update');
    Route::delete('/delete-panelist/{id}', [PanelistController::class, 'destroy'])->name('panelist.destroy');
    Route::get('/view-panelists/{id}', [PanelistController::class, 'viewPanelist'])->name('view_panelists');
    Route::get('/view-panelist/{id}', [PanelistController::class, 'viewSinglePanelist'])->name('view_panelist');

    // Group
    Route::get('/add-group', [GroupController::class, 'create'])->name('group.create');
    Route::get('/view-group/{id}', [GroupController::class, 'viewGroup']);
    Route::get('/update-group/{id}', [GroupController::class, 'updateGroupForm']);
    Route::post('/update-group/{id}', [GroupController::class, 'updateGroup'])->name('group.update');
    Route::delete('/delete-group/{id}', [GroupController::class, 'deleteGroup'])->name('groups.delete');

    // Instructor
    Route::get('/add-instructor', [InstructorController::class, 'create'])->name('instructor.create');
    Route::get('/view-instructor/{id}', [InstructorController::class, 'viewInstructor']);
    Route::get('/update-instructor/{id}', [InstructorController::class, 'updateInstructorForm']);
    Route::post('/update-instructor/{id}', [InstructorController::class, 'updateInstructor'])->name('instructor.update');
    Route::delete('/delete-instructor/{id}', [InstructorController::class, 'deleteInstructor'])->name('instructor.delete');

    // Capstones
    Route::get('/capstones-list', [CapstoneController::class, 'index'])->name('capstones.list');
    Route::get('/update-capstone/{ids}', [CapstoneController::class, 'create'])->where('ids', '.*');
    Route::post('/update-capstone/{ids}', [CapstoneController::class, 'update'])->name('capstone.update');

    // Activity Log
    Route::get('/activity-log', [ActivityLogController::class, 'index']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/bell-notifications', [NotificationController::class, 'bellNotifications'])->name('bell.notifications');

    // Custom Reminders
    Route::post('/create-custom-reminder', [NotificationController::class, 'storeCustomReminder'])->name('custom.reminder');

    // Capstone history
    Route::get('/capstone-history/{id?}', [CapstoneController::class, 'history'])->name('capstone.history');

    // Instructor Code
    Route::get('/code', [AuthController::class, 'code']);
    Route::post('/add-code', [AuthController::class, 'addCode'])->name('code.create');

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.view');
    Route::post('/update-profile/{id}', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Test Scheduler for debugging
    Route::get('/test-scheduler', [AdminController::class, 'runSmartScheduler']);
    
    // Test mail
    Route::get('mail-test', [AuthController::class, 'sendTestMail']);
});
