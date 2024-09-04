<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\OwnerController; // Import the OwnerController
use App\Http\Controllers\UserController; // Import the UserController
use App\Http\Controllers\TeamController; // Import the TeamController
use App\Services\UserService;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Password reset routes
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');


// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});


// Owner routes
Route::middleware(['auth:sanctum', 'role:Owner'])->group(function () {
    Route::post('/owner/role-permissions', [OwnerController::class, 'setRolePermissions']);
    Route::post('/owner/add-permissions', [OwnerController::class, 'addPermissions']);
});


// User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('user/account-settings', [UserController::class, 'updateAccountSettings']); // Route for user to update their account settings
    Route::post('user/add-user', [UserController::class, 'addUser']);
    // Route to delete a user account
    Route::delete('/user/delete-account', [UserController::class, 'deleteAccount']);

    
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('team/add', [TeamController::class, 'addTeam']);
    Route::post('team/add-members', [TeamController::class, 'addTeamMembers']); // Route to add team members
    Route::post('team/set-teamlead', [TeamController::class, 'setTeamLead']); // Route to set team lead
    Route::post('team/remove-member', [TeamController::class, 'removeTeamMember']); // Route to remove team member
});
