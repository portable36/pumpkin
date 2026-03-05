<?php

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

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'namespace' => 'Modules\AdvanceReport\Http\Controllers', 'middleware' => ['auth', 'locale', 'permission', 'web']], function () {
    Route::get('advance-reports', 'AdvanceReportController@index')->name('advance-reports');
    Route::get('advance-reports/{slug}', 'AdvanceReportController@show')->name('advance-reports.show');
    Route::get('advance-reports/{slug}/export', 'AdvanceReportController@export')->name('advance-reports.export');
});

Route::group(['prefix' => 'vendor', 'namespace' => 'Modules\AdvanceReport\Http\Controllers\Vendor', 'middleware' => ['auth', 'locale', 'permission', 'web']], function () {
    Route::get('advance-reports', 'AdvanceReportController@index')->name('vendor.advance-reports');
    Route::get('advance-reports/{slug}', 'AdvanceReportController@show')->name('vendor.advance-reports.show');
    Route::get('advance-reports/{slug}/export', 'AdvanceReportController@export')->name('vendor.advance-reports.export');
});
