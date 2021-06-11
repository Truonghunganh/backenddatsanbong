<?php

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
$url= "App\Http\Controllers\API\V1";
Route::prefix('v1')->group(function () {
    /// user  lllllllllllll
    Route::resource('quan', "App\Http\Controllers\API\V1\QuanController")->only(['index','show', 'destroy']);
    Route::resource('san', "App\Http\Controllers\API\V1\SanController")->only(['show']);
    Route::resource('datsans', 'App\Http\Controllers\API\V1\DatSanController')->only(['store', 'destroy']);
    Route::post('getQuanByIdAndTokenUser', 'App\Http\Controllers\API\V1\QuanController@getQuanByIdAndTokenUser');
    
    Route::get('getListDatSanByUserToken', 'App\Http\Controllers\API\V1\DatSanController@getListDatSanByUserToken');
    Route::put('editUserByToken', 'App\Http\Controllers\API\V1\UserController@editUserByToken');
    // Route::post('registerUser', 'App\Http\Controllers\Api\V1\UserController@registerUser');
    
    // Route::post('loginUser', 'App\Http\Controllers\Api\V1\UserController@loginUser')->name('loginUser');
    Route::get('checkTokenUser', 'App\Http\Controllers\API\V1\CheckTokenController@checkTokenUser');
    //Route::get('getAllQuanDangHoatdongByUser', 'App\Http\Controllers\Api\V1\QuanController@getAllQuanDangHoatdongByUser');
    Route::post('reviewByUser', 'App\Http\Controllers\API\V1\ReviewController@reviewByUser');
    Route::get('getDatSansvaSansByUserAndIdquanAndNgay', 'App\Http\Controllers\API\V1\DatSanController@getDatSansvaSansByUserAndIdquanAndNgay');
    Route::resource('comments', 'App\Http\Controllers\API\V1\CommentController')->only(['index', 'store','update', 'destroy']);
    Route::get('searchListQuans', 'App\Http\Controllers\API\V1\QuanController@searchListQuans');
    

    Route::get('getAllCommentCuaMotQuan', 'App\Http\Controllers\API\V1\BinhLuanController@getAllCommentCuaMotQuan');
    
    // chủ quán : quản lý các quán của mình 
    Route::get('checkTokenInnkeeper', 'App\Http\Controllers\API\V1\CheckTokenController@checkTokenInnkeeper');
    // Route::post('loginInnkeeper', 'App\Http\Controllers\API\V1\ChuQuanController@loginInnkeeper');
    // Route::post('registerInnkeeper', 'App\Http\Controllers\API\V1\ChuQuanController@registerInnkeeper');
    Route::put('editInnkeeperByToken', 'App\Http\Controllers\API\V1\ChuQuanController@editInnkeeperByToken');

    Route::get('getQuanByIdAndTokenInnkeeper', 'App\Http\Controllers\API\V1\QuanController@getQuanByIdAndTokenInnkeeper');
    Route::get('getListQuansByTokenInnkeeper', 'App\Http\Controllers\API\V1\QuanController@getListQuansByTokenInnkeeper');
    Route::get('getListQuansByTokenInnkeeperChuaPheDuyet', 'App\Http\Controllers\API\V1\QuanController@getListQuansByTokenInnkeeperChuaPheDuyet');

    Route::post('addQuanByInnkeeper', 'App\Http\Controllers\API\V1\QuanController@addQuanByInnkeeper');
    Route::post('editQuanByTokenInnkeeper', 'App\Http\Controllers\API\V1\QuanController@editQuanByTokenInnkeeper');
    Route::post('addSanByInnkeeper', 'App\Http\Controllers\API\V1\SanController@addSanByInnkeeper');
    Route::get('getSanByInnkeeperVaId', 'App\Http\Controllers\API\V1\SanController@getSanByInnkeeperVaId');


    Route::post('checkTokenInnkeeperAndIdquan', 'App\Http\Controllers\API\V1\CheckTokenController@checkTokenInnkeeperAndIdquan');
    Route::delete('deleteQuanChuaduyetByInnkeeper', 'App\Http\Controllers\API\V1\QuanController@deleteQuanChuaduyetByInnkeeper');
    Route::put('editSanByInnkeeper', 'App\Http\Controllers\API\V1\SanController@editSanByInnkeeper');

    Route::post('checkTokenInnkeeperAndIdsan', 'App\Http\Controllers\API\V1\CheckTokenController@checkTokenInnkeeperAndIdsan');
    Route::post('getDoanhThuByInnkeeper', 'App\Http\Controllers\API\V1\DoanhThuController@getDoanhThuByInnkeeper');
    Route::post('thayDoiDatSanByInnkeeper', 'App\Http\Controllers\API\V1\DatSanController@thayDoiDatSanByInnkeeper');
    Route::post('getListDatSanByInnkeeper', 'App\Http\Controllers\API\V1\DatSanController@getListDatSanByInnkeeper');
    Route::post('getAllDatSanByInnkeeperAndIdquan', 'App\Http\Controllers\API\V1\DatSanController@getAllDatSanByInnkeeperAndIdquan');
    Route::put('xacNhanDatsanByInnkeeper', 'App\Http\Controllers\API\V1\DatSanController@xacNhanDatsanByInnkeeper');
    Route::post('getTongDoanhThuTheoNamByInnkeeper', 'App\Http\Controllers\API\V1\DoanhThuController@getTongDoanhThuTheoNamByInnkeeper');
    Route::post('getDatSansvaSansByInnkeeperAndIdquanAndNgay', 'App\Http\Controllers\API\V1\DatSanController@getDatSansvaSansByInnkeeperAndIdquanAndNgay');
    Route::post('getChiTietDanhthuByInnkeeper', 'App\Http\Controllers\API\V1\DoanhThuController@getChiTietDanhthuByInnkeeper');
    Route::post('thayDoiTrangthaiSanByInnkeeper', 'App\Http\Controllers\API\V1\SanController@thayDoiTrangthaiSanByInnkeeper');
    Route::get('getAllCommentCuaMotQuanByInnkeeper', 'App\Http\Controllers\API\V1\CommentController@getAllCommentCuaMotQuanByInnkeeper');
    Route::get('getListQuansByTrangthaiChoHome', 'App\Http\Controllers\API\V1\QuanController@getListQuansByTrangthaiChoHome');

    //Route::get('get', 'App\Http\Controllers\Api\V1\CheckTokenController@checkTokenInnkeeper');getAllCommentCuaMotQuanByInnkeeper
     
    Route::get('thu', 'App\Http\Controllers\API\V1\CheckTokenController@thu');
    
    // admin : quản lý các quán getDatSansvaSansByInnkeeperAndIdquanAndNgay,Route::post('getDoanhThuByInnkeeper', 'App\Http\Controllers\Api\V1\DoanhThuController@getDoanhThuByInnkeeper');
    
    Route::get('checkTokenAdmin', 'App\Http\Controllers\API\V1\CheckTokenController@checkTokenAdmin');
    // Route::post('loginAdmin', 'App\Http\Controllers\API\V1\AdminController@loginAdmin');
    Route::put('editAdminByToken', 'App\Http\Controllers\API\V1\AdminController@editAdminByToken');
    Route::get('getListQuansDaPheDuyetByTokenAdmin', 'App\Http\Controllers\API\V1\QuanController@getListQuansDaPheDuyetByTokenAdmin');
    Route::get('getListQuansChuaPheDuyetByTokenAdmin', 'App\Http\Controllers\API\V1\QuanController@getListQuansChuaPheDuyetByTokenAdmin');
    Route::put('UpdateTrangThaiQuanTokenAdmin', 'App\Http\Controllers\API\V1\QuanController@UpdateTrangThaiQuanTokenAdmin');
    Route::post('getDoanhThuByAdmin', 'App\Http\Controllers\API\V1\DoanhThuController@getDoanhThuByAdmin');
    Route::post('getDoanhThuListQuanByAdmin', 'App\Http\Controllers\API\V1\DoanhThuController@getDoanhThuListQuanByAdmin');
    Route::post('getDoanhThuCuaAdminTheoNam', 'App\Http\Controllers\API\V1\DoanhThuController@getDoanhThuCuaAdminTheoNam');
    Route::post('getDatSansvaSansByAdminAndIdquanAndNgay', 'App\Http\Controllers\API\V1\DatSanController@getDatSansvaSansByAdminAndIdquanAndNgay');
    Route::post('getChiTietDanhthuCuaMotQuanByAdmin', 'App\Http\Controllers\API\V1\DoanhThuController@getChiTietDanhthuCuaMotQuanByAdmin');
    Route::post('getTongDoanhCuaMotQuanThuTheoNamByAdmin', 'App\Http\Controllers\API\V1\DoanhThuController@getTongDoanhCuaMotQuanThuTheoNamByAdmin');
    Route::post('getDoanhThuListQuanCuaMotNamByAdmin', 'App\Http\Controllers\API\V1\DoanhThuController@getDoanhThuListQuanCuaMotNamByAdmin');
    Route::get('getAllCommentCuaMotQuanByAdmin', 'App\Http\Controllers\API\V1\CommentController@getAllCommentCuaMotQuanByAdmin');
    Route::resource('users', 'App\Http\Controllers\API\V1\UserController')->only(['index']);
    Route::put('editUserByAdmin', 'App\Http\Controllers\API\V1\UserController@editUserByAdmin');
    Route::get('searchUsersByAdmin', 'App\Http\Controllers\API\V1\UserController@searchUsersByAdmin');
    

    Route::post('login', 'App\Http\Controllers\API\V1\CheckTokenController@login');
    Route::get('checkToken', 'App\Http\Controllers\API\V1\CheckTokenController@checkToken');
    Route::post('register', 'App\Http\Controllers\API\V1\UserController@register');
    


});

// Route::middleware('auth:api')->get('/user', function (Request $request) {getTongDoanhCuaMotQuanThuTheoNamByAdmin
// getDanhThuCuaAdminTheoNam    return $request->user();getDoanhThuListQuanCuaMotNamByAdmin
// });getDatSansvaSansByAdminAndIdquanAndNgay; getChiTietDanhthuCuaMotQuanByAdmin
