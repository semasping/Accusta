<?php

namespace App\Widgets;

use App\Http\Controllers\CuratorRewardsController;
use App\Http\Middleware\CheckHistoryAcc;
use App\Repositories\CuratorRewards;
use App\Repositories\TransferToVesting;
use App\semas\BchApi;
use App\semas\GolosApi;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Date\Date;
use MongoDB;
use phpDocumentor\Reflection\Types\Integer;

class VPTransactions extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The number of seconds before each reload.
     *
     * @var int|float
     */
    public $reloadTimeout = 600;

    /**
     * The number of minutes before cache expires.
     * False means no caching at all.
     *
     * @var int|float|bool
     */
    //public $cacheTime = 60;

    /**
     * Cache tags allow you to tag related items in the cache
     * and then flush all cached values that assigned a given tag.
     *
     * @var array
     */
    //public $cacheTags = ['vpgp', 'curation_rewards'];

    public function placeholder()
    {
        return 'Checking transactions...';
    }

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {

        //uncomment this on 1 december!!!!!!!!!!!!!!!!!!

        /*if (getenv('APP_ENV') == 'production') {
            //$this->cacheTags[] = $this->config['account'];
            $acc = str_replace('@', '', $this->config['account']);
            $acc = mb_strtolower($acc);
            $this->config['account'] = trim($acc);
            //$checkResult = CheckHistoryAcc::doCheck($this->config['account']);
            $checkResult = [];
            $checkResult['result'] == true;
            if ($checkResult['result'] == false) {
                echo '
            <div class="container-fluid">
                <div class="row">
                    <br>
                    <br>
                    Обрабатываю историю аккаунта. Ждите.<br>
                    Обработано ' . $checkResult['processed'] . ' из ' . $checkResult['max'] . '.<br>
                    Страница будет обновляться в процесс обработки.
                </div>
            </div>
    ';

            }

        }*/

        $transfers = TransferToVesting::get($this->config['account'], Date::createFromDate('2017', '08', '01'),
            Date::createFromDate('2018', '08', '01')->endOfMonth());
        //dump($transfers);
        $sum = 0;
        foreach ($transfers as $item) {
            $sum = $sum + str_replace(' GOLOS', '', $item['amount']);
        }
        $transfers = (collect($transfers))->map(function ($item) {
            $arr = $item;
            $arr['sum'] = str_replace(' GOLOS', '', $item['amount']);
            $arr['date_diff'] = Date::createFromDate('2018', '08',
                '1')->endOfMonth()->diffInDays(Date::createFromFormat('Y F d h:i', $arr['date']));
            //$arr['date_diff'] = Date::createFromDate('2018','08','1')->endOfMonth()->diffInDays(Date::createFromTimeString($arr['date']));
            $arr['perc'] = $arr['date_diff'] * 0.0169;
            $arr['itog'] = ($arr['sum'] * $arr['perc'] / 100) + $arr['sum'];
            return $arr;
        });

        $account_data = Cache::rememberForever($this->config['account'], function () {
            return GolosApi::getAccountFull($this->config['account']);
        });
        $vs = $account_data[0]['vesting_shares'];
        $sp = GolosApi::convertToSg((int)$vs);
        $golos = $account_data[0]['balance'];
        $gbg = $account_data[0]['sbd_balance'];


        return view('golos.VP.widgets.transactions', [
            'config' => $this->config,
            'acc' => $this->config['account'],
            'data' => $transfers,
            'sum' => $sum,
            'sp' => $sp,
            'golos' => $golos,
            'gbg' => $gbg,
        ]);
    }
}
