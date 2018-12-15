<?php

namespace App\Widgets;

use App\AccountTransaction;
use App\semas\BchApi;
use App\Services\Charts;
use Arrilot\Widgets\AbstractWidget;
use Jenssegers\Date\Date;
use MongoDB;

class WitnessRewards extends AbstractWidget
{
    public $cacheTime = 1;
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run($acc, $date = false)
    {
        $summs['all'] = 0;
        $dataChart = [];
        $chartRewardsSP = [];
        $acc = $this->config['account'];

        $data = $this->getRewardsIn($this->config['account']);
        $sums_by_monthes = $data['sums_by_monthes'];
        $summs = $data['allSP'];

        $res_arr = $sums_by_monthes;
        ksort($res_arr);
        foreach ($res_arr as $key => $sums_by_month) {
            $fm = Date::parse($key . '01')->format('Y M');

            $dataChart['month'][] = $fm;
            $dataChart['date'][] = Date::parse($key . '01')->timestamp;
            $dataChart['total'][] = (BchApi::convertToSg($sums_by_month['value']));
            $dataChart['count'][] = $sums_by_month['count'];

        }
        if (!empty($dataChart)) {
            $chartRewardsSP = $this->getChartRewardsIn($dataChart, $this->config['account']);
        }

        return view(getenv('BCH_API') . '.widgets.witness_rewards', [
            'config' => $this->config,
            'account' => $acc,
            'acc' => $acc,
            'summs' => $summs,
            'summs_by_monthes' => $sums_by_monthes,
            'chartRewardsSP' => $chartRewardsSP,
        ]);

    }

    public function placeholder()
    {
        return 'Check witness rewards...';
    }

    public function getRewardsIn($acc)
    {

        $collection = BchApi::getMongoDbCollection($acc);

        $sums_by_monthes = $collection->aggregate([
            ['$match' => ['type' => ['$eq' => 'producer_reward']]],
            ['$unwind' => '$op'],
            [
                '$match' => ['op.VESTS'=>['$gte'=>0]],
            ],
            [
                '$group' => [
                    '_id' => ['date' => ['M' => ['$month' => '$date'], 'Y' => ['$year' => '$date'],]],
                    'total' => ['$sum' => '$op.VESTS'],
                    'count' => ['$sum' => 1]
                ]
            ],
        ]);
        $monthes = [];
        foreach ($sums_by_monthes as $state) {
                        $date = Date::parse('01.' . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);
            $r['date'] = $date->format('Y F');
            $r['value'] = $state['total'];
            $r['count'] = $state['count'];
            $monthes[$date->format('Ym')] = $r;
        }
        krsort($monthes);

        $summs['all'] = 0;
        $sums_all = $collection->aggregate([
            ['$match' => ['type' => ['$eq' => 'producer_reward']]],
            ['$unwind' => '$op'],
            ['$group' => ['_id' => null, 'total' => ['$sum' => '$op.VESTS']]],
        ]);
        foreach ($sums_all as $state) {
            $summs['all'] = $state['total'];
        }

        return ['allSP' => $summs, 'sums_by_monthes' => $monthes];
    }

    private function getChartRewardsIn($data, $acc)
    {
        $labels = [
            'dataset1' => __(getenv('BCH_API') . '.shares'),
            'dataset2' => __(getenv('BCH_API') . '.count_rewards'),
            'title' => __(getenv('BCH_API') . '.title_witness_rewards_shares'),
            'name' => 'spChart'
        ];

        return Charts::getChartRewards($data, $acc, $labels);

    }
}
