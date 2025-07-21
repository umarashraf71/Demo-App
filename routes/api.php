<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\QaLabTestController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\PlantController;


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

Route::post('login', [UsersController::class, 'login']);

Route::post('logout', [UsersController::class, 'logout']);
Route::post('send-reset-otp', [UsersController::class, 'sendResetpasswordOtp']);
Route::post('reset-password', [UsersController::class, 'resetPasswordWithOtp']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user-detail', [UsersController::class, 'userDetail']);
    Route::post('change-password', [UsersController::class, 'changePassword']);
    Route::get('qa-lab-test', [QaLabTestController::class, 'index']);

    Route::get('/suppliers', [\App\Http\Controllers\Api\ApiController::class, 'getSuppliers']);
    Route::get('/cp-suppliers', [\App\Http\Controllers\Api\ApiController::class, 'getCpSuppliers']);
    Route::get('/cps', [\App\Http\Controllers\Api\ApiController::class, 'getCps']);
    Route::get('/area-offices', [\App\Http\Controllers\Api\ApiController::class, 'getAOs']);
    Route::post('/purchase-mcc', [\App\Http\Controllers\Api\ApiController::class, 'purchaseSave']);
    Route::post('/purchase-mcc-bulk', [\App\Http\Controllers\Api\ApiController::class, 'bulkPurchaseSave']);
    Route::post('/cp-purchase-mmt', [\App\Http\Controllers\Api\ApiController::class, 'purchaseMMTSave']);
    Route::post('/cp-purchase-mmt-bulk', [\App\Http\Controllers\Api\ApiController::class, 'bulkPurchaseMMTSave']);
    Route::post('/ao-purchase-supplier-milk', [\App\Http\Controllers\Api\ApiController::class, 'purchaseAtAreaOffice']);
    Route::post('/ao-purchase-supplier-milk-bulk', [\App\Http\Controllers\Api\ApiController::class, 'bulkPurchaseAtAreaOffice']);
    Route::post('/plant-purchase-supplier-milk', [\App\Http\Controllers\Api\ApiController::class, 'purchaseAtPlant']);
    Route::post('/plant-purchase-cp-milk', [\App\Http\Controllers\Api\ApiController::class, 'purchaseByPlant']);
    Route::get('/milk-reception-ao', [\App\Http\Controllers\Api\ApiController::class, 'aoMilkReceptionCp']);
    Route::get('/milk-reception-mcc', [\App\Http\Controllers\Api\ApiController::class, 'milkReceptionCp']);
    Route::post('/milk-reception-mcc', [\App\Http\Controllers\Api\ApiController::class, 'milkReceptionAtMCC']);
    Route::post('/milk-reception-mcc-bulk', [\App\Http\Controllers\Api\ApiController::class, 'milkReceptionAtMCCbulk']);
    //handshake apis
    Route::post('/handshake-mcc', [\App\Http\Controllers\Api\ApiController::class, 'handshakeMCC']);

    Route::post('/dispatch-mmt-to-plant', [\App\Http\Controllers\Api\ApiController::class, 'MMTDispatchPlant']);
    Route::post('/dispatch-ao-to-plant', [\App\Http\Controllers\Api\ApiController::class, 'AODispatchPlant']);
    Route::get('/mmts', [\App\Http\Controllers\Api\ApiController::class, 'mmts']);
    Route::post('/milk-reception-ao', [\App\Http\Controllers\Api\ApiController::class, 'milkReceptionAtAO']);
    Route::get('/lacto-meter-readings', [\App\Http\Controllers\Api\ApiController::class, 'getLactoMeterReadings']);
    Route::get('/my-route-today', [\App\Http\Controllers\Api\ApiController::class, 'getRoute']);
    Route::post('/route-close', [\App\Http\Controllers\Api\ApiController::class, 'closeTodayRoute']);
    Route::get('/get-all-vehicles', [\App\Http\Controllers\Api\ApiController::class, 'getAllVehicles']);
    Route::get('/get-area-office-balance', [\App\Http\Controllers\Api\ApiController::class, 'getAreaOfficeBalance']);
    Route::get('/get-mmt-balance', [\App\Http\Controllers\Api\ApiController::class, 'getMmtBalance']);
    Route::get('/get-plant-dispatch-report', [\App\Http\Controllers\Api\ApiController::class, 'getPlantDispatchReport']);

    // start Transfers
    Route::get('/mcc-to-mcc-collection-centers', [\App\Http\Controllers\Api\TransferController::class, 'mccToMccCCs']);
    Route::post('/request-transfer-mcc-to-mcc', [\App\Http\Controllers\Api\TransferController::class, 'mccToMccSaveRequest']);
    Route::post('/transfer-mcc', [\App\Http\Controllers\Api\TransferController::class, 'transferMCCToMCC']);
    Route::get('/transfer-mcc-status', [\App\Http\Controllers\Api\TransferController::class, 'transferMCCStatus']);
    Route::get('/get-current-mcc-transfer-request', [\App\Http\Controllers\Api\TransferController::class, 'getCurrMCCTransferReq']);
    Route::get('/get-current-area-office-transfer-request', [\App\Http\Controllers\Api\TransferController::class, 'getCurrAreaOfficeTransferReq']);
    Route::get('/get-all-plants', [\App\Http\Controllers\Api\TransferController::class, 'getPlants']);

    Route::get('/ao-to-ao-aos', [\App\Http\Controllers\Api\TransferController::class, 'aoToAoAreaOffices']);
    Route::post('/request-transfer-ao-to-ao', [\App\Http\Controllers\Api\TransferController::class, 'aoToAoSaveRequest']);
    Route::get('/transfer-ao-status', [\App\Http\Controllers\Api\TransferController::class, 'transferAoStatus']);
    Route::post('/mmt-transfer-to-ao', [\App\Http\Controllers\Api\TransferController::class, 'MmtToOtherAoSaveRequest']);
    Route::post('/transfer-ao', [\App\Http\Controllers\Api\TransferController::class, 'transferAoToAo']);
    //end Transfers
    Route::get('/mprs', [\App\Http\Controllers\Api\ApiController::class, 'mprs']);
    Route::get('/area-offices', [\App\Http\Controllers\Api\ApiController::class, 'areaOffices']);
    Route::get('/mpr/{id}', [\App\Http\Controllers\Api\ApiController::class, 'mpr']);
    Route::get('/get-report', [\App\Http\Controllers\Api\ReportController::class, 'index']);
    Route::get('/get-ao-lab-report', [\App\Http\Controllers\Api\ReportController::class, 'aoLabReport']);
    Route::get('/get-plant-report', [\App\Http\Controllers\Api\ReportController::class, 'plantReport']);
    Route::get('/route/{id}/check-in/', [RouteController::class, 'checkIn']);
    Route::get('/route/{id}/check-out', [RouteController::class, 'checkOut']);
    Route::post('/upate-locations', [RouteController::class, 'updateLocations']);
    Route::get('/route/{id}/status', [RouteController::class, 'RouteStatus']);
    Route::get('get-cp-mmt', [\App\Http\Controllers\Api\ApiController::class, 'getCpmmt']);
    Route::get('/get-assigned-route-details', [RouteController::class, 'getRouteVehicle']);

    Route::get('/get-assigned-route-details', [RouteController::class, 'getRouteVehicle']);

    //plant flow
    Route::post('generate-token-number', [PlantController::class, 'GeneratetokenNumber']);
    Route::get('get-tokens', [PlantController::class, 'getTokens']);
    Route::post('plant-reception-qa', [PlantController::class, 'plantReceptionqa']);
    Route::post('plant-reception-weight', [PlantController::class, 'plantReceptionweight']);
    Route::post('plant-purchase-weight', [PlantController::class, 'plantpurchaseweight']);
    Route::get('get-plant-reception', [PlantController::class, 'getPlantreception']);
    Route::get('cip', [PlantController::class, 'cip']);
    Route::get('gate-out', [PlantController::class, 'gateOut']);
});



Route::fallback(function () {
    return response()->json(['errorMessage' => 'Api Not Found!', 'isSuccessful' => false,], 200);
});