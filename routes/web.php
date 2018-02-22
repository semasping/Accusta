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


use App\AccountTransaction;
use App\Http\Middleware\CheckHistoryAcc;
use App\semas\GolosApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/_form_submit', function (Request $request) {
    //dump('in form');
    /*if ($request->has('acc')) {
        $acc = ($request['acc']);
        $max = GolosApi::getHistoryAccountLast($acc);
        $current = GolosApi::getCurrentProcessedHistoryTranzId($acc);

        if ($current < $max - 2000){

            GolosApi::getHistoryAccountFullInCache($acc);
            return redirect()->action('TransAccController@showProcessTranz',$request->all());
        }
        dump($current, $max);

    }*/
    $params = $request->all();
    $params['acc'] = $request['acc'];
    return redirect()->action($request->get('controller'), $params);

});

//Route::get('/test/', function (){ \App\semas\BchApi::getBlock('111111111');});
Route::get('info',function (){
    phpinfo();
});
Route::get('mt',function (){
    \App\semas\SteemitApi::getHistoryAccountFullInDBDesc('prc');
    /*$at = new AccountTransaction();
    $tr = $at->where('account', 'semasping')->groupBy('type')->get(['type', 'block']);
    dump($tr->toArray());*/
});
Route::get('mongo-test',function (){
    $transaction = \App\semas\SteemitApi::getHistoryAccount('semasping',1,0);
    $transaction =$transaction[0][1];
    dump($transaction);
    $accTr = new AccountTransaction();
    $accTr->account = 'semasping';
    $accTr->trx_id = $transaction['trx_id'];
    $accTr->block = $transaction['block'];
    $accTr->timestamp = $transaction['timestamp'];
    $accTr->type = $transaction['op'][0];
    $accTr->op = $transaction['op'];
    $accTr->save();
    dump($transaction);
    dump($accTr);
});

if (getenv('BCH_API') == 'golos') {
    Route::get('/@{acc}', 'TransAccController@index')->middleware(CheckHistoryAcc::class)->name('home');
    Route::get('/@{acc}/by_month', 'TransAccController@index')->name('trans_by_month')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/by_weeks', 'TransAccController@indexByWeek')->name('trans_by_week')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/transaction_history', 'TransHistoryController@show')->name('trans_history')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/_transaction_history', 'TransHistoryController@dt_show')->name('trans_history_dt_show')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/_export_xls_transaction_history', 'TransHistoryController@exportToExcel')->name('trans_history_show_export_xls');//->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/sg', 'TransAccController@indexSg')->name('trans_sg')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/process_tranz', 'TransAccController@showProcessTranz');
}

if (getenv('BCH_API') == 'steemit') {
    Route::get('/@{acc}', 'TransAccController@index')->middleware(CheckHistoryAcc::class)->name('home');
    Route::get('/@{acc}/by_month', 'TransAccController@index')->name('trans_by_month')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/by_weeks', 'TransAccController@indexByWeek')->name('trans_by_week')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/transaction_history', 'TransHistoryController@show')->name('trans_history')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/_transaction_history', 'TransHistoryController@dt_show')->name('trans_history_dt_show')->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/sg', 'TransAccController@indexSg')->name('trans_sg')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/process_tranz', 'TransAccController@showProcessTranz');
    Route::get('/@{acc}/{page}', 'TransAccController@inProcess');

}



