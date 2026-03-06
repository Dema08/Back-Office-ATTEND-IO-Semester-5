<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IotFaceController;
use App\Http\Controllers\AttendioApiController;
use App\Http\Controllers\RfidApiController;


Route::post('/iot/face-event', [IotFaceController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// endpoint ATTEND-IO untuk poin fokus
Route::post('/attendio/focus-event', [AttendioApiController::class, 'storeFocusEvent']);
Route::post('/attendio/rfid-absen', [AttendioApiController::class, 'storeRfidAttendance']);
Route::get('/attendio/pertemuan-status/{pertemuan}', [AttendioApiController::class, 'pertemuanStatus']);
Route::get('/attendio/active-pertemuan', [AttendioApiController::class, 'getActivePertemuan']);
Route::get('/attendio/rfid-lookup',    [AttendioApiController::class, 'rfidLookup']);
Route::post('/attendio/rfid-scan',     [AttendioApiController::class, 'rfidScan']);
Route::get('/attendio/rfid-last-scan', [AttendioApiController::class, 'rfidLastScan']);
Route::get('/attendio/rfid-mode', [AttendioApiController::class, 'rfidMode']);
Route::post('/attendio/rfid-mode', [AttendioApiController::class, 'setRfidMode']);

// DEBUG: cek apakah api.php kepanggil
Route::get('/debug-attendio', function () {
    return response()->json([
        'ok'    => true,
        'route' => '/api/debug-attendio',
    ]);
});
