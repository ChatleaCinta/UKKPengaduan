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
Route::middleware(['cors'])->group(function(){
    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');
    
    Route::get('pdf', 'PengaduanController@pdfview');
    
    Route::get('pengaduan/{id}', 'PengaduanController@show');
    Route::get('pengaduan', 'PengaduanController@index');
    
    Route::middleware(['jwt.verify'])->group(function(){
        Route::get('login/check', 'UserController@getAuthenticatedUser');   
        Route::post('logout', 'UserController@logout');
        Route::post('pengaduan', 'PengaduanController@store');
        Route::post('pengaduan/edit/{id}', 'PengaduanController@edit');
        Route::delete('pengaduan/{id}', 'PengaduanController@delete');
        Route::get('pengaduanku/', 'PengaduanController@myIndex');
    });
    Route::middleware(['jwt.verifyAdmin'])->group(function(){
        Route::post('admin/register', 'UserController@registerByAdmin');
        Route::post('edituser/{id}', 'UserController@changeStatus');
        Route::post('tanggapan/{id_pengaduan}', 'TanggapanController@store');
        Route::put('tanggapan/{id}', 'TanggapanController@edit');
        Route::delete('tanggapan/{id}', 'TanggapanController@delete');
        Route::get('user', 'UserController@index');
        Route::delete('user/{id}', 'UserController@delete');
    });
});