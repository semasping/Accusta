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


use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/_form_submit', function (Request $request){

    return redirect()->action($request->get('controller'),$request->all());

});


Route::get('/@{acc}', 'TransAccController@index');
Route::get('/@{acc}/by_month', 'TransAccController@index')->name('trans_by_month');
Route::get('/@{acc}/by_weeks', 'TransAccController@indexByWeek')->name('trans_by_week');
Route::get('/@{acc}/transaction_history', 'TransHistoryController@show')->name('trans_history');
Route::get('/@{acc}/_transaction_history', 'TransHistoryController@dt_show')->name('trans_history_dt_show');
Route::get('/@{acc}/sg', 'TransAccController@indexSg')->name('trans_sg');

