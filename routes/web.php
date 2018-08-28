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
use App\semas\BchApi;
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
    $rrr = BchApi::GetDynamicGlobalProperties();
    dump($rrr);
    $rrr = BchApi::getPost('golosboard','golosboard-notify-germanlifestyle-20180525t130726000z');
    dump(($rrr));
    /*$at = new AccountTransaction();
    $tr = $at->where('account', 'semasping')->groupBy('type')->get(['type', 'block']);
    dump($tr->toArray());*/
});
Route::get('testSendToken', function (){
    $accounts = [
        0 => [
            'to'=>'semasping',
            'amount'=>'0.001 STEEM',
            'memo'=>'test',
        ],
        1 => [
            'to'=>'semasping',
            'amount'=>'0.001 STEEM',
            'memo'=>'test2',
        ],
        2 => [
            'to'=>'semasping',
            'amount'=>'0.001 STEEM',
            'memo'=>'test3',
        ],
        3 => [
            'to'=>'semasping',
            'amount'=>'0.001 STEEM',
            'memo'=>'test4',
        ]
    ];
    $from = 'semasping';
    $key = getenv('STEEM_SEMASPING_ACTIVE_WIF');
    //$key = getenv('GOLOS_SEMASPING_ACTIVE_WIF');
    \App\semas\SteemitApi::sendTokenToMany($accounts,$from,$key);
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
    //Route::get('/vox-populi/', 'TransAccController@index')->middleware(CheckHistoryAcc::class)->name('home');



    Route::get('/@{acc}', 'AuthorRewardsController@showAll')->middleware(CheckHistoryAcc::class)->name('home');
    Route::get('/@{acc}/by_month', 'AuthorRewardsController@showAll')->name('trans_by_month')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/author/by_month/{type}/{date}', 'AuthorRewardsController@getDataTableRewardsByMonth')->name('trans_author_by_mont')->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/by_weeks', 'AuthorRewardsController@showAll')->name('trans_by_week')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/transaction_history', 'TransHistoryController@show')->name('trans_history')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/_transaction_history', 'TransHistoryController@dt_show')->name('trans_history_dt_show')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/_export_xls_transaction_history', 'TransHistoryController@exportToExcel')->name('trans_history_show_export_xls');//->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/sg', 'PowerUpDownController@showAll')->name('trans_sg')->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/curator', 'CuratorRewardsController@showAll')->name('trans_curator')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/curator/by_month/{type}/{date}', 'CuratorRewardsController@getDataTableRewardsByMonth')->name('trans_curator_by_mont')->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/benefactor', 'BenefactorRewardsController@showAll')->name('trans_benefactor')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/benefactor/by_month/{type}/{date}', 'BenefactorRewardsController@getDataTableRewardsByMonth')->name('trans_benefactor_by_mont')->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/process_tranz', 'TransAccController@showProcessTranz');

    Route::get('/@{acc}/witness_votes', 'WitnessPageController@show')->name('witness_votes')->middleware(CheckHistoryAcc::class);


    Route::get('/@{acc}/authors_rewards', 'AuthorRewardsController@showAll')->name('author_rewards_all');//->middleware(CheckHistoryAcc::class);
}

if (getenv('BCH_API') == 'steemit') {
    Route::get('/@{acc}', 'TransAccController@index')->middleware(CheckHistoryAcc::class)->name('home');
    Route::get('/@{acc}/by_month', 'AuthorRewardsController@showAll')->name('trans_by_month')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/author/by_month/{type}/{date}', 'AuthorRewardsController@getDataTableRewardsByMonth')->name('trans_author_by_mont')->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/by_weeks', 'TransAccController@indexByWeek')->name('trans_by_week')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/transaction_history', 'TransHistoryController@show')->name('trans_history')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/_transaction_history', 'TransHistoryController@dt_show')->name('trans_history_dt_show')->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/sg', 'PowerUpDownController@showAll')->name('trans_sg')->middleware(CheckHistoryAcc::class);


    Route::get('/@{acc}/curator', 'CuratorRewardsController@showAll')->name('trans_curator')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/curator/by_month/{type}/{date}', 'CuratorRewardsController@getDataTableRewardsByMonth')->name('trans_curator_by_mont')->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/benefactor', 'BenefactorRewardsController@showAll')->name('trans_benefactor')->middleware(CheckHistoryAcc::class);
    Route::get('/@{acc}/benefactor/by_month/{type}/{date}', 'BenefactorRewardsController@getDataTableRewardsByMonth')->name('trans_benefactor_by_mont')->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/process_tranz', 'TransAccController@showProcessTranz');
    //Route::get('/@{acc}/{page}', 'TransAccController@inProcess');

    Route::get('/@{acc}/witness_votes', 'WitnessPageController@show')->name('witness_votes')->middleware(CheckHistoryAcc::class);

    Route::get('/@{acc}/rewards', 'RewardsPageController@showAll')->name('rewards_page')->middleware(CheckHistoryAcc::class);



}



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
