<?php

use App\Http\Controllers\BasePriceController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\IncentiveTypeController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RouteVehicleController;
use App\Http\Controllers\SupplierTestBaseIncentiveController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\AreaOfficeController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CollectionPointController;
use App\Http\Controllers\SupplierTypeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VendorProfileController;
use App\Http\Controllers\MilkCollectionVehicleController;
use App\Http\Controllers\QaLabTestController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InventoryitemTypeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\PaymentProcessController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/check-instance', function () {
    return response()->json([
        'instance' => gethostname(),
        'ip' => request()->ip(),
        'timestamp' => now(),
    ]);
});

//auth routes
Route::middleware('web')->group(function () {
    Auth::routes();
});


Route::view('change-password-view', 'content.authentication.auth-change-password-cover')->name('change.password.view');
Route::post('change-password', [UsersController::class, 'changePassword'])->name('password.change');

//theme settings routes
Route::post('change-theme-mode', [CommonController::class, 'changeThememode'])->name('chnage.theme.mode');
// Main Page Route
Route::redirect('/', 'login');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [DashboardController::class, 'index']);
    Route::resource('permissions', PermissionsController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('users', UsersController::class);
    Route::post('get-access-level-data', [UsersController::class, 'getAccesslevelData'])->name('get.access.level.dropdown');
    Route::post('get-access-level-parent', [UsersController::class, 'getAccesslevelParent'])->name('get.access.level.parent');
    Route::post('update-status', [CommonController::class, 'updateStatus']);
    /* Route Dashboards */
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [DashboardController::class, 'dashboardAnalytics'])->name('dashboard-analytics');
        Route::get('ecommerce', [DashboardController::class, 'dashboardEcommerce'])->name('dashboard-ecommerce');
    });
    /* Route Dashboards */

    /* Route for plant */
    Route::resource('plant', PlantController::class);


    /* end for plant */

    /* Route for dept */
    Route::resource('dept', DepartmentController::class);
    /* end for dept*/

    /* Route for section */
    Route::resource('section', SectionController::class);
    /* end for section*/

    /* Route for zones */
    Route::resource('zone', ZoneController::class);
    /* end for zones*/

    /* Route for area offices */
    Route::resource('area-office', AreaOfficeController::class);
    Route::post('add-ao-owner', [AreaOfficeController::class, 'addOwner'])->name('add.ao.owner');
    Route::get('ao-owner-status-update', [AreaOfficeController::class, 'ownerUpdateStatus'])->name('ao.owner.update.status');
    Route::post('add-ao-agreement', [AreaOfficeController::class, 'addAgreement'])->name('add.ao.agreement');
    Route::get('ao-agreement-status-update', [AreaOfficeController::class, 'agreementUpdateStatus'])->name('ao.agreement.update.status');


    /* end for area offices */

    /* Route for CollectionPoint*/
    Route::resource('collection-point', CollectionPointController::class);
    Route::resource('categories', CategoriesController::class);
    Route::post('add-cp-agreement', [CollectionPointController::class, 'addAgreement'])->name('add.cp.agreement');
    Route::post('add-owner', [CollectionPointController::class, 'addOwner'])->name('add.cp.owner');
    Route::post('add-chiller', [CollectionPointController::class, 'addChiller'])->name('add.cp.chiller');
    Route::post('add-generator', [CollectionPointController::class, 'addGenerator'])->name('add.cp.generator');
    Route::post('add-chiller', [CollectionPointController::class, 'addChiller'])->name('add.cp.chiller');
    Route::get('cp-agreement-status-update', [CollectionPointController::class, 'agreementUpdateStatus'])->name('cp.agreement.update.status');
    Route::get('cp-generator-status-update', [CollectionPointController::class, 'generatorUpdateStatus'])->name('cp.generator.update.status');
    Route::get('cp-chiller-status-update', [CollectionPointController::class, 'chillerUpdateStatus'])->name('cp.chiller.update.status');
    Route::get('cp-generator-delete', [CollectionPointController::class, 'generatorDelete'])->name('cp.generator.delete');
    Route::get('cp-chiller-delete', [CollectionPointController::class, 'chillerDelete'])->name('cp.chiller.delete');
    Route::get('cp-owner-status-update', [CollectionPointController::class, 'ownerUpdateStatus'])->name('cp.owner.update.status');
    Route::get('area-office-daily-record/{officeId}', [DashboardController::class, 'areaOfficeDailyRecord'])->name('area.office.daily.record');
    /* end for CollectionPoint*/

    /* Route for supplier type*/
    Route::resource('source-type', SupplierTypeController::class);
    Route::post('update-source-type', [SupplierTypeController::class, 'sourceTypeUpdate'])->name('source.type.update');
    Route::get('supplier-delivery-confoguration', [SupplierTypeController::class, 'deliveryConfigview'])->name('supplier.type.delivery.configuration');
    Route::post('supplier-delivery-confoguration-store', [SupplierTypeController::class, 'deliveryConfigstore'])->name('supp.type.config.store');
    Route::group(['prefix' => 'pricing', 'as' => 'price.'], function () {
        Route::get('source-type', [BasePriceController::class, 'supplierType'])->name('supplier.type');
        Route::get('suppliers', [BasePriceController::class, 'suppliers'])->name('suppliers');
        Route::get('supplier-collection-points', [BasePriceController::class, 'supplierCollectionPoint'])->name('supplier.collection.point');
        Route::get('source-type-collection-point', [BasePriceController::class, 'sourceTypeCollectionPoint'])->name('source.type.collection.point');
        Route::get('collection-point', [BasePriceController::class, 'collectionPoint'])->name('collection.point');
        Route::get('get-filters-dropdown-data', [BasePriceController::class, 'getFilterDropdownData'])->name('get.dropdown.data');
        Route::get('get-cps', [BasePriceController::class, 'getCps'])->name('get.cps');
    });

    Route::get('supplier-incentive-configuration', [SupplierTypeController::class, 'incentiveConfigview'])->name('supplier.incentive.configuration');
    Route::post('supplier-incentive-confoguration-store', [SupplierTypeController::class, 'incentiveConfigstore'])->name('supplier.incentive.config.store');
    Route::get('supplier-incentives', [IncentiveController::class, 'index'])->name('supplier.incentives');
    Route::post('add-incentive', [IncentiveController::class, 'save'])->name('add.incentive');

    Route::group(['prefix' => 'routes', 'as' => 'routes.'], function () {
        Route::get('/', [RouteController::class, 'index'])->name('index');
        Route::post('save', [RouteController::class, 'store'])->name('save');
        Route::post('update', [RouteController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [RouteController::class, 'destroy'])->name('delete');
        Route::get('show', [RouteController::class, 'show'])->name('show');
        Route::get('get-collection-points', [RouteController::class, 'getCollectionPoints'])->name('get.cps');
    });

    /* Route for supplier*/
    Route::resource('supplier', SupplierController::class);
    Route::post('add-agreement', [SupplierController::class, 'addAgreement'])->name('add.agreement');
    Route::get('agreement-status-update', [SupplierController::class, 'agreementUpdateStatus'])->name('agreement.update.status');
    Route::get('price-update', [SupplierController::class, 'updatePrice'])->name('price.update');
    Route::get('payment-process-update', [SupplierController::class, 'updatePaymentProcess'])->name('payment.process.update.status');
    Route::post('price-status-update', [BasePriceController::class, 'statusUpdate'])->name('price.status.update');
    Route::post('price-update-create', [BasePriceController::class, 'addCpPrice'])->name('cp.price.create');
    Route::post('cp-price-create', [BasePriceController::class, 'addCpSourceTypePrice'])->name('cp.st.price.create');
    Route::get('cp-price-update', [BasePriceController::class, 'updateCpPrice'])->name('cp.price.update');
    Route::get('price-delete/{id}', [BasePriceController::class, 'priceDelete'])->name('price.delete');

    Route::get('get-types-data', [SupplierController::class, 'getTypeWiseData'])->name('get.type.wise.data');
    Route::get('get-collection-point-data', [SupplierController::class, 'getCollectionPointData'])->name('getcollectionpointdata');

    Route::get('get-supplier-cps', [BasePriceController::class, 'getSupplierCollectionpoints'])->name('get.supplier.cps');
    Route::get('purchase-receptions', [CollectionPointController::class, 'getPurchaseReceptionMMT'])->name('get.purchase.receptions');
    Route::get('plant-receptions', [CollectionPointController::class, 'getPlantReception'])->name('get.plant.receptions');
    Route::get('plant-reception-detail/{id}', [CollectionPointController::class, 'plantReceptionDetail'])->name('plantreception.detail');


    Route::get('purchase-receptions-ao', [CollectionPointController::class, 'getPurchaseReceptionAO'])->name('get.purchase.receptions.ao');
    Route::get('purchases', [CollectionPointController::class, 'getPurchases'])->name('get.purchases');
    Route::get('rejections', [CollectionPointController::class, 'rejection'])->name('get.rejections');
    Route::get('rejection-details/{id}', [CollectionPointController::class, 'rejectionDetails'])->name('get.rejectionDetails');
    Route::get('purchased-rejections', [CollectionPointController::class, 'purchasedRejections'])->name('get.purchasedRejections');
    Route::get('purchased-rejection-details/{id}', [CollectionPointController::class, 'purchasedRejectionDetails'])->name('get.purchasedRejectionDetails');
    Route::get('mps/{serial_number?}', [CollectionPointController::class, 'mps'])->name('mps');
    Route::get('mr/{serial_number?}', [CollectionPointController::class, 'mr'])->name('mr');
    Route::delete('delete-purchase/{id}', [CollectionPointController::class, 'deletePurchase']);
    Route::get('purchase-details/{id}', [CollectionPointController::class, 'purchaseDetails'])->name('purchase.view');
    Route::delete('delete-reception/{id}', [CollectionPointController::class, 'deleteReception']);
    Route::get('reception-details/{id}', [CollectionPointController::class, 'receptionDetails'])->name('reception.view');
    /* end for supplier*/

    Route::resource('route-vehicle', \App\Http\Controllers\RouteVehicleController::class);
    Route::get('route-vehicle-open/{id}', [RouteVehicleController::class, 'openRoute'])->name('route-vehicle.open.route');
    Route::get('view-vehicle-visted-routes/{id}', [\App\Http\Controllers\RouteVehicleController::class, 'viewVistedRoutesOnMap'])->name('view-vehicle.routes');
    Route::post('route-vehicle/updatee', [RouteVehicleController::class, 'update'])->name('route-vehicle.updatee');


    /* Route for Vendor Profile*/
    Route::resource('vendor-profile', VendorProfileController::class);
    Route::post('add-vendor-agreement', [VendorProfileController::class, 'addAgreement'])->name('add.vendor.agreement');
    Route::get('vendor-agreement-status-update', [VendorProfileController::class, 'agreementUpdateStatus'])->name('vendor.agreement.update.status');


    /* end for Vendor Profile*/

    /* Route for MCVehicle*/
    Route::resource('mc-vehicle', MilkCollectionVehicleController::class);
    /* end for MCVehicle*/

    /* Route for QaLabTest*/
    Route::resource('qa-labtest', QaLabTestController::class);
    /* end for QaLabTest*/

    /* Route for TestUnit */
    Route::resource('test-uom', MeasurementUnitController::class);
    /* end for TestUnit */

    /* Route for Inventory Items*/
    Route::resource('inventory-item', InventoryItemController::class);
    /* end for Inventory Items*/

    /* Route for Inventory Items Type*/
    Route::resource('inventory-item-type', InventoryitemTypeController::class);
    /* end for Inventory Items*/

    /* Route for Customers*/
    Route::resource('customer', CustomerController::class);
    /* end for Customers*/

    Route::resource('payment-calculation',PaymentProcessController::class);
    Route::post('payment-process',[PaymentProcessController::class , 'paymentProcess'])->name('payment.process');
    Route::get('payment-calculation-approve/{id}',[PaymentProcessController::class , 'paymentCalculationapprove'])->name('payment.calculation.approve');
    Route::get('payment-details/{id}',[PaymentProcessController::class , 'showPurchases'])->name('payment.details');

    /* Route for workflow*/
    Route::resource('workflow', WorkflowController::class);
    Route::get('get-workflow-document-type-roles',[WorkflowController::class , 'getWorkflowDocumentTypeRoles'])->name('get.workflow.documentType.roles');
    Route::get('/workflow-approvals', [\App\Http\Controllers\WorkFlowApprovalController::class, 'index'])->name('workflow.approvals.index');
    Route::get('/workflow-approvals/update-status', [\App\Http\Controllers\WorkFlowApprovalController::class, 'updateStatus'])->name('workflow.approvals.status.update');
    Route::get('/workflow-approvals/milk-transfer/update-status', [\App\Http\Controllers\WorkFlowApprovalController::class, 'updateStatusMT'])->name('workflow.mt.approvals.status.update');
    Route::get('workflow/batch-prices/{code}', [BasePriceController::class, 'batchPricesListing'])->name('approval.show');
    Route::get('workflow/transfer/{code}', [\App\Http\Controllers\WorkFlowApprovalController::class, 'transferRequestDetail']);
    Route::get('/get-remarks-ajax', [BasePriceController::class, 'getRemarks'])->name('get.remarks.ajax');
    Route::get('/get-chiller-Detail/{inventoryItem}', [InventoryItemController::class, 'chillerDetail'])->name('get.chiller.detail');
    //Route::get('/get-prices-ajax', [BasePriceController::class,'getPrices'])->name('get.prices.ajax');

    /* end for workflow*/
    //give permissions to super admin
    Route::get('super-admin-permissions', [CommonController::class, 'superAdminpermissions']);
    Route::group(['prefix' => 'base-pricing', 'as' => 'price.'], function () {
        Route::get('/', [BasePriceController::class, 'index'])->name('index');
        Route::get('/created', [BasePriceController::class, 'create'])->name('create');
        Route::get('/pending', [BasePriceController::class, 'pending'])->name('pending');
        Route::get('/batch/{code?}', [BasePriceController::class, 'batchDetail']);
        Route::get('/rejected', [BasePriceController::class, 'rejected'])->name('rejected');
        Route::get('/reverted', [BasePriceController::class, 'reverted'])->name('reverted');
        Route::get('/add', [BasePriceController::class, 'savePrice'])->name('add');
        Route::get('/get-data', [BasePriceController::class, 'getDropdownData'])->name('fetch.data');
        Route::get('/get-cps-source-type', [BasePriceController::class, 'getCpsaccordingSourcetype'])->name('cp.source.type');
        Route::get('/delete', [BasePriceController::class, 'delete'])->name('del');
        Route::get('/delete-rejected', [BasePriceController::class, 'deleteRejected'])->name('rejected.delete');
        Route::get('/send-for-approval', [BasePriceController::class, 'sendForApproval'])->name('send.for.approval');

        Route::get('/edit-prices', [BasePriceController::class, 'edit'])->name('edit');
        Route::get('/update-price', [BasePriceController::class, 'updatePrice'])->name('send.for.update');
    });

    Route::group(['prefix' => 'incentives', 'as' => 'incentives.'], function () {
        Route::get('/index', [IncentiveController::class, 'index'])->name('index');
        Route::post('/store', [IncentiveController::class, 'store'])->name('store');
        Route::post('/update', [IncentiveController::class, 'update'])->name('update');
        Route::get('/types', [IncentiveTypeController::class, 'index'])->name('types');
        Route::post('type/store', [IncentiveTypeController::class, 'store'])->name('type.store');
        Route::post('type/update', [IncentiveTypeController::class, 'update'])->name('type.update');
        Route::get('type/delete/{id}', [IncentiveTypeController::class, 'destroy'])->name('type.destroy');
        Route::post('update-status', [IncentiveController::class, 'statusUpdate'])->name('status.update');
        Route::post('type-update-status', [IncentiveTypeController::class, 'statusUpdate'])->name('type.status.update');
        Route::get('get-incentive-types', [IncentiveController::class, 'getIncentiveTypes'])->name('get.types');
        Route::get('/test-based-suppliers-incentives', [SupplierTestBaseIncentiveController::class, 'index'])->name('test.based');
        Route::post('save-test-based-supplier-incentive', [SupplierTestBaseIncentiveController::class, 'save'])->name('save.test.base.supplier.incentive');
        Route::post('update-test-based-supplier-incentive', [SupplierTestBaseIncentiveController::class, 'update'])->name('update.test.base.supplier.incentive');
        Route::post('update-test-supplier-incentive-status', [SupplierTestBaseIncentiveController::class, 'statusUpdate'])->name('test.base.supplier.status.update');
    });

    /**
     * Reports Routes Start Here
     */
    Route::get('fresh-milk-purchase-summary', [ReportsController::class, 'freshMilkpurchaseSummary'])->name('milk.purchase.summary');
    Route::post('fresh-milk-purchase-export', [ReportsController::class, 'freshMilkexportReport'])->name('milk.purchase.summary.exportfile');
    Route::post('get-collection-points', [ReportsController::class, 'getCollectionPoints'])->name('getcollectionpoints');

    //
    Route::get('area-office-collection-summary', [ReportsController::class, 'areaOfficeCollectionSummary'])->name('ao.collection.summary');
    Route::post('export-file-summary', [ReportsController::class, 'exportFile'])->name('exportfile');
    Route::post('get-collection-points', [ReportsController::class, 'getCollectionPoints'])->name('getcollectionpoints');


    /**
     * Reports Routes End
     */

    Route::get('notifications', [CommonController::class, 'notifications'])->name('notifications');
    Route::get('transfers', [CommonController::class, 'transfers'])->name('transfers');
    Route::get('dispatches', [CommonController::class, 'listMilkDispatches'])->name('dispatches');
    Route::get('get-dispatches', [CommonController::class, 'listMilkDispatches'])->name('get.dispatches');
    Route::get('dispatch-details/{id}', [CommonController::class, 'milkDispatchDetails'])->name('dispatch.details');
    Route::get('banks', [\App\Http\Controllers\BankController::class, 'index'])->name('banks.index');
    Route::post('bank/store', [\App\Http\Controllers\BankController::class, 'store'])->name('bank.store');
    Route::post('bank/update', [\App\Http\Controllers\BankController::class, 'update'])->name('bank.update');
    Route::get('districts', [\App\Http\Controllers\DistrictController::class, 'index'])->name('districts.index');
    Route::post('district/store', [\App\Http\Controllers\DistrictController::class, 'store'])->name('district.store');
    Route::post('district/update', [\App\Http\Controllers\DistrictController::class, 'update'])->name('district.update');
    Route::delete('district/delete/{district}', [\App\Http\Controllers\DistrictController::class, 'destroy'])->name('district.delete');
    Route::get('tehsils', [\App\Http\Controllers\TehsilController::class, 'index'])->name('tehsils.index');
    Route::get('logs', [\App\Http\Controllers\LogController::class, 'index'])->name('logs.index');
    Route::post('tehsil/store', [\App\Http\Controllers\TehsilController::class, 'store'])->name('tehsil.store');
    Route::post('tehsil/update', [\App\Http\Controllers\TehsilController::class, 'update'])->name('tehsil.update');
    Route::get('get-tehsils/{id}', [\App\Http\Controllers\TehsilController::class, 'getTehsils'])->name('get.tehsils');
    Route::delete('tehsil/delete/{tehsil}', [\App\Http\Controllers\TehsilController::class, 'destroy'])->name('tehsil.delete');
    
    Route::get('import-collection-points', [\App\Http\Controllers\DashboardController::class, 'importCollectionpoints']);
    Route::get('import-inventory-items', [\App\Http\Controllers\DashboardController::class, 'importInventoryitems']);
    Route::get('import-suppliers', [\App\Http\Controllers\DashboardController::class, 'importSuppliers']);
});
