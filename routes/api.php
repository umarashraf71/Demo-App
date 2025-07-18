<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Master\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function()
{
    Route::post('/users', [AuthController::class, 'index']);
});


//Admin Portal routes and api's
Route::group(['middleware' => 'AdminDBSwitch', 'prefix' => 'admin'], function()
{

    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('register', [AdminAuthController::class, 'register']);  

    Route::post('createBox', [AdminBoxController::class, 'createBox']);
    Route::post('sendActivationCode', [AdminBoxController::class, 'sendBoxActivationCode']);

    Route::group(['middleware' => 'auth:sanctum'], function()
    {
        Route::post('/users', [AdminAuthController::class, 'index']);
    });

});



Route::group(['prefix' => 'customer'], function()
{
    Route::post('login', [CustomerAuthController::class, 'login']);
    Route::post('register', [CustomerAuthController::class, 'register']);
    Route::post('forgotPassword', [CustomerAuthController::class, 'forgotPassword']);
    Route::post('resetPassword', [CustomerAuthController::class, 'resetPassword']);


    //Customer api routes and CustomerSwitch middleware which will switch db on every request
    //But currently we are using a single customer db
    Route::group(['middleware' => 'CustomerDBSwitch', 'prefix' => 'customer'], function()
    {
        //protected routes with respect to every database
        Route::group(['middleware' => 'auth:sanctum'], function()
        {
            Route::post('/users', [CustomerAuthController::class, 'index']);

            Route::post('activateBrainbox', [CustomerBoxController::class, 'activateBrainbox']);
            Route::post('getAllBoxes', [CustomerBoxController::class, 'getAllBoxes']);
        });

    });

});

