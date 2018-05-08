<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 25.03.2018
 * Time: 16:23
 */

namespace App\Http\Controllers;


use App\semas\BchApi;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Maatwebsite\Excel\Facades\Excel;
use PragmaRX\Tracker\Vendor\Laravel\Facade as Tracker;


class BenefactorRewardsController extends Controller
{
    public function showAll(Request $request, $_acc = '')
    {
        $res_arr = [];
        $month = [];
        $acc = $request->get('acc');
        if (empty($acc)) {
            if (empty($_acc)) {
                return view(getenv('BCH_API') . '.trans.notfound', [
                    'account' => $_acc,
                    'form_action' => 'TransAccController@index',
                ]);
            }
            $acc = $_acc;

        }
        $acc = str_replace('@', '', $acc);
        $acc = mb_strtolower($acc);
        $acc = trim($acc);

        /*        Tracker::trackEvent(['event' => 'PowerUpDown for @'.$acc]);
                Tracker::trackEvent(['event' => '@'.$acc]);*/


        if ($request->csv) {
            /*Tracker::trackEvent(['event' => 'CSV PowerUpDown']);*/
            return true;
            //return $this->exportToExcel($author->toArray(), 'BenefactorRewards', $acc);
        }

        /*Tracker::trackEvent(['event' => 'PowerUpDown']);*/


        $dataIn = $this->getRewardsIn($acc);
        $chartRewardsIn = $this->getChartRewardsIn($dataIn, $acc);
        $dataOut = $this->getRewardsOut($acc);
        $chartRewardsOut = $this->getChartRewardsOut($dataOut, $acc);
        //dd($wv_by_month);
        $key = $acc;
        $account = $acc;
        $date = Date::now();
        $form_action = 'BenefactorRewardsController@showAll';
        return view(getenv('BCH_API') . '.trans.index-benefactor-rewards', [
            'account' => $acc,
            'acc' => $acc,
            'form_action' => $form_action,
            'date' => false,
            //'wv_by_month' => $wv_by_month,
            'month' => $month,
            'week' => true,
            'chartRewardsIn' => $chartRewardsIn,
            'chartRewardsOut' => $chartRewardsOut,
        ]);
        //return view(env('BCH_API').'.rewards', compact('author', 'key','account','date','form_action'));
        //dump($res_arr);
    }

    public function exportToExcel($data, $type, $acc)
    {
        Excel::create($type . '_' . $acc, function ($excel) use ($data, $type, $acc) {

            $excel->sheet($type, function ($sheet) use ($data) {

                $sheet->fromArray($data);

            });

        })->download('csv');
    }

    public function getRewardsIn($acc)
    {
        $res_arr = [];
        $data = [];
        $collection = BchApi::getMongoDbCollection($acc);
        $data_by_monthes = $collection->aggregate([
            [
                '$match' => [
                    'type' => ['$eq' => 'comment_benefactor_reward'],
                    'op.benefactor' => ['$eq' => $acc]
                ]
            ],
            ['$unwind' => '$op'],
            [
                '$group' => [
                    '_id' => ['date' => ['M' => ['$month' => '$date'], 'Y' => ['$year' => '$date'],]],
                    'total' => ['$sum' => '$op.VESTS'],
                    'count' => ['$sum' => 1]
                ]
            ],
        ]);
        foreach ($data_by_monthes as $state) {
            //dd($state);
            $date = Date::parse('01.' . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);
            //$arr['date'] = Date::parse($state['timestamp'])->format('Y F d h:i');

            $arr['total'] = $state['total'];
            $arr['count'] = $state['count'];
            $arr['date'] = $date->endOfMonth();
            $res_arr[$date->format('Ym')] = $arr;
        }
        ksort($res_arr);
        foreach ($res_arr as $key => $item) {
            $fm = Date::parse($key . '01')->format('Y M');
            $data['total'][] = (BchApi::convertToSg($item['total']));
            $data['totalVests'][] = $item['total'];
            $data['count'][] = $item['count'];
            $data['month'][] = $fm;
        }


        return $data;
    }

    public function getRewardsOut($acc)
    {
        $res_arr = [];
        $data = [];
        $collection = BchApi::getMongoDbCollection($acc);
        $data_by_monthes = $collection->aggregate([
            [
                '$match' => [
                    'type' => ['$eq' => 'comment_benefactor_reward'],
                    'op.benefactor' => ['$ne' => $acc]
                ]
            ],
            ['$unwind' => '$op'],
            [
                '$group' => [
                    '_id' => ['date' => ['M' => ['$month' => '$date'], 'Y' => ['$year' => '$date'],]],
                    'total' => ['$sum' => '$op.VESTS'],
                    'count' => ['$sum' => 1]
                ]
            ],
        ]);
        foreach ($data_by_monthes as $state) {
            //dd($state);
            $date = Date::parse('01.' . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);
            //$arr['date'] = Date::parse($state['timestamp'])->format('Y F d h:i');

            $arr['total'] = $state['total'];
            $arr['count'] = $state['count'];
            $arr['date'] = $date->endOfMonth();
            $res_arr[$date->format('Ym')] = $arr;
        }
        ksort($res_arr);
        foreach ($res_arr as $key => $item) {
            $fm = Date::parse($key . '01')->format('Y M');
            $data['total'][] = (BchApi::convertToSg($item['total']));
            $data['totalVests'][] = $item['total'];
            $data['count'][] = $item['count'];
            $data['month'][] = $fm;
        }


        return $data;
    }

    private function getChartRewardsIn($data, $acc)
    {

        $chartjs = app()->chartjs
            ->name('lineChartTest')
            ->type('line')
            ->size(['width' => 400, 'height' => 200])
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
                    "label" => "Count of Benefactor rewards",
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
                                text: 'Benefactor Rewards statistics for @" . $acc . "'
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

    private function getChartRewardsOut($data, $acc)
    {

        $chartjs = app()->chartjs
            ->name('lineChartOut')
            ->type('line')
            ->size(['width' => 400, 'height' => 200])
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
                                text: 'Benefactor Rewards from your post to others accounts'
                            },
                            
                            scales: {
                                yAxes: [{
                                    type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                    display: true,
                                    position: 'left',
                                    id: 'y-axis-1',
                                    scaleLabel: {display: true, labelString: 'SP Reward'},
                                }],
                            }
					    }");
        return $chartjs;
    }

}