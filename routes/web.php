<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ManufacturingController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DailyConditionsController;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\VesselsController;
use App\Http\Controllers\MaintenanceController;
use App\Models\Equipment;

Route::middleware(['auth','isActive'])->group(function () {

Route::get('/', function () {

        $ready = Equipment::where([['conditions' , 1], ['is_delete', 0]])->count();
        $working = Equipment::where([['conditions' , 2], ['is_delete', 0]])->count();
        $notWorking = Equipment::where([['conditions' , 3], ['is_delete', 0]])->count();
        $maintanance =  Equipment::where([['conditions' , 4], ['is_delete', 0]])->count();
        // $code = \OCR::scan('IMG.jpg');

        return view('dashboard',[
            'ready' =>$ready,
            'working' =>$working,
            'notWorking' =>$notWorking,
            'maintanance' =>$maintanance
        ]);    
    })->name('home');


Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/notifications', [AuthController::class, 'notifications'])->name('notifications');
Route::get('/notificationsData', [AuthController::class, 'notificationsData'])->name('notificationsData');

Route::resource('users', UserController::class);
Route::get('/usersData', [UserController::class, 'usersData'])->name('usersData');

Route::get('/equipmentsData', [EquipmentController::class, 'equipmentsData'])->name('equipmentsData');
Route::get('/equipments/trash', [EquipmentController::class, 'trash'])->name('/equipments/trash');
Route::get('/equipmentsTrash', [EquipmentController::class, 'equipmentsTrash'])->name('equipmentsTrash');
Route::delete('/equipments/restore/{id}', [EquipmentController::class, 'restore'])->name('/equipments/restore/{id}');
Route::resource('equipments', EquipmentController::class);

Route::get('/manufacturingData', [ManufacturingController::class, 'manufacturingData'])->name('manufacturingData');
Route::post('/manufacturing/done', [ManufacturingController::class, 'done'])->name('/manufacturing/done');
Route::get('/manufacturing/trash', [ManufacturingController::class, 'trash'])->name('/manufacturing/trash');
Route::get('/manufacturingTrash', [ManufacturingController::class, 'manufacturingTrash'])->name('manufacturing');
Route::delete('/manufacturing/restore/{id}', [ManufacturingController::class, 'restore'])->name('/manufacturing/restore/{id}');
Route::resource('manufacturing', ManufacturingController::class);

Route::get('/buildings/trash', [BuildingController::class, 'trash'])->name('/buildings/trash');
Route::get('/buildingsTrash', [BuildingController::class, 'buildingsTrash'])->name('buildingsTrash');
Route::delete('/buildings/restore/{id}', [BuildingController::class, 'restore'])->name('/buildings/restore/{id}');
Route::get('/buildingsData', [BuildingController::class, 'buildingsData'])->name('buildingsData');
Route::resource('buildings', BuildingController::class);

Route::get('/workshops/trash', [WorkshopController::class, 'trash'])->name('/workshops/trash');
Route::get('/workshopsTrash', [WorkshopController::class, 'workshopsTrash'])->name('workshopsTrash');
Route::delete('/workshops/restore/{id}', [WorkshopController::class, 'restore'])->name('/workshops/restore/{id}');
Route::get('/workshopsData', [WorkshopController::class, 'workshopsData'])->name('workshopsData');
Route::resource('workshops', WorkshopController::class);

Route::get('/warehouses/trash', [WarehouseController::class, 'trash'])->name('/warehouses/trash');
Route::get('/warehousesTrash', [WarehouseController::class, 'warehousesTrash'])->name('warehousesTrash');
Route::delete('/warehouses/restore/{id}', [WarehouseController::class, 'restore'])->name('/warehouses/restore/{id}');
Route::get('/warehousesData', [WarehouseController::class, 'warehousesData'])->name('warehousesData');
Route::resource('warehouses', WarehouseController::class);

Route::get('/dailyConditions/trash', [DailyConditionsController::class, 'trash'])->name('/equipments/trash');
Route::get('/dailyConditionsTrash', [DailyConditionsController::class, 'dailyConditionsTrash'])->name('dailyConditionsTrash');
Route::delete('/dailyConditions/restore/{id}', [DailyConditionsController::class, 'restore'])->name('/dailyConditions/restore/{id}');
Route::get('/dailyConditionsData', [DailyConditionsController::class, 'dailyConditionsData'])->name('dailyConditionsData');
Route::get('/dailyConditions/print/{id}', [DailyConditionsController::class, 'print'])->name('/dailyConditions/print/{id}');
Route::post('/dailyConditions/save', [DailyConditionsController::class, 'save'])->name('/dailyConditions/save');
Route::resource('dailyConditions', DailyConditionsController::class);

Route::get('/requests/print/{id}', [RequestsController::class, 'print'])->name('/requests/print/{id}');
Route::get('/requests/printOut/{id}', [RequestsController::class, 'printOut'])->name('/requests/printOut/{id}');
Route::get('/requestsData', [RequestsController::class, 'requestsData'])->name('requestsData');
Route::post('/requests/out', [RequestsController::class, 'out'])->name('/requests/out');
Route::put('/requests/out/{id}', [RequestsController::class, 'outUpdate'])->name('/requests/out');
Route::get('/requestOut', [RequestsController::class, 'requestOut'])->name('/requestOut');
Route::get('/outData', [RequestsController::class, 'outData'])->name('outData');
Route::get('/requests/{id}/editOut', [RequestsController::class, 'editOut'])->name('editOut');
Route::get('/requests/done/{id}', [RequestsController::class, 'done'])->name('/requests/done');
Route::put('/requests/done/{id}', [RequestsController::class, 'doneUpdate'])->name('/requests/done');
Route::resource('requests', RequestsController::class);

Route::get('/vessels/print/{id}', [VesselsController::class, 'print'])->name('/vessels/print/{id}');
Route::get('/vesselsData', [VesselsController::class, 'vesselsData'])->name('vesselsData');
Route::put('/vessels/done/{id}', [VesselsController::class, 'done'])->name('/vessels/done');
Route::resource('vessels', VesselsController::class);

Route::get('/maintenance/print/{id}', [MaintenanceController::class, 'print'])->name('/maintenance/print/{id}');
Route::get('/maintenanceData', [MaintenanceController::class, 'maintenanceData'])->name('maintenanceData');
Route::put('/maintenance/done/{id}', [MaintenanceController::class, 'done'])->name('/maintenance/done');
Route::resource('maintenance', MaintenanceController::class);

});

Route::get('/login', function () { return view('login');})->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');


