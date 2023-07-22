<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialAnalayzeController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('login');
});
Route::post('/login_user', [LoginController::class, 'loginUser']);
Auth::routes();

Route::get('/home', [HomeController::class, 'index']);

Auth::routes();

//material
Route::get('material', [MaterialController::class,'index']);
Route::get('material/create', [MaterialController::class,'create']);
Route::get('material/edit/{id}', [MaterialController::class,'edit']);
Route::get('material/delete/{id}', [MaterialController::class,'delete']);
Route::post('material/store', [MaterialController::class,'store']);
Route::post('material/update/{id}', [MaterialController::class,'update']);

//transaksi
Route::get('transaksi', [TransaksiController::class,'index']);
Route::get('transaksi/create', [TransaksiController::class,'create']);
Route::get('transaksi/edit/{id}', [TransaksiController::class,'edit']);
Route::get('transaksi/delete/{id}', [TransaksiController::class,'delete']);
Route::post('transaksi/store', [TransaksiController::class,'store']);
Route::post('transaksi/update/{id}', [TransaksiController::class,'update']);

//material_analyze
Route::get('material_analyze', [MaterialAnalayzeController::class,'index']);