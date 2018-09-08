<?php

namespace App\Widgets;

use App\Http\Controllers\CuratorRewardsController;
use App\Http\Middleware\CheckHistoryAcc;
use App\Repositories\CuratorRewards;
use App\semas\BchApi;
use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Date\Date;
use MongoDB;

class CurationsRewards extends AbstractWidget
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
        return 'Checking rewards...';
    }

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {

        if (getenv('APP_ENV') == 'production') {
            //$this->cacheTags[] = $this->config['account'];
            $acc = str_replace('@', '', $this->config['account']);
            $acc = mb_strtolower($acc);
            $this->config['account'] = trim($acc);
            $checkResult = CheckHistoryAcc::doCheck($this->config['account']);
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

        }

        $curatorRewards = CuratorRewards::get($this->config['account'], Date::createFromDate('2017', '09', '01'),
            Date::createFromDate('2018', '08', '01')->endOfMonth());
        //dump($curatorRewards);

        return view('golos.VP.widgets.curations_rewards', [
            'config' => $this->config,
            'acc' => $this->config['account'],
            'data' => $curatorRewards,
        ]);
    }
}
