<?php

namespace App\Widgets;

use App\AccountTransaction;
use App\semas\BchApi;
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

        $chartjs = app()->chartjs
            ->name('lineChartTest')
            ->type('line')
            ->size(['width' => 400, 'height' => 100])
            ->labels($data['month'])
            ->datasets([
                [
                    "label" => "Steem Power",
                    'backgroundColor' => "rgba(38, 185, 154, 0.31)",
                    'borderColor' => "rgba(38, 185, 154, 0.7)",
                    "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                    "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                    "pointHoverBackgroundColor" => "#fff",
                    "pointHoverBorderColor" => "rgba(220,220,220,1)",
                    'data' => $data['total'],
                    'yAxisID' => 'y-axis-1',

                ],
                [
                    "label" => "Count of rewards",
                    'backgroundColor' => "rgba(138, 185, 154, 0.31)",
                    'borderColor' => "rgba(138, 185, 154, 0.7)",
                    "pointBorderColor" => "rgba(138, 185, 154, 0.7)",
                    "pointBackgroundColor" => "rgba(138, 185, 154, 0.7)",
                    "pointHoverBackgroundColor" => "#fff",
                    "pointHoverBorderColor" => "rgba(220,220,220,1)",
                    'data' => $data['count'],
                    'yAxisID' => 'y-axis-2',
                ]
            ])
            ->optionsRaw("{
                            responsive: true,
                            tooltips: {
                                mode: 'index',
                                intersect: false
                            },
                            hover: {
                                mode: 'index',
                                intersect: false
                            },
                            stacked: false,
                            title: {
                                display: true,
                                text: 'Witness Rewards statistics for @" . $acc . "'
                            },
                            
                            scales: {
                                yAxes: [{
                                    type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                    display: true,
                                    position: 'left',
                                    id: 'y-axis-1',
                                    scaleLabel: {display: true, labelString: 'SP Reward'},
                                }, {
                                    type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                    display: true,
                                    position: 'right',
                                    id: 'y-axis-2',
                                    scaleLabel: {display: true, labelString: 'Count rewards'},
        
                                    // grid line settings
                                    gridLines: {
                                        drawOnChartArea: false, // only want the grid lines for one axis to show up
                                    },
                                }],
                            }
					    }");
        return $chartjs;
    }
}
