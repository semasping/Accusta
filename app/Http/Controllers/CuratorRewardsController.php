<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 25.03.2018
 * Time: 16:23
 */

namespace App\Http\Controllers;


use App\semas\BchApi;
use Exception;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Maatwebsite\Excel\Facades\Excel;
use MongoDB;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Component\PageTotalsRow;
use ViewComponents\Grids\Component\TableCaption;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Component\Control\PageSizeSelectControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Data\ArrayDataProvider;
use ViewComponents\ViewComponents\Input\InputSource;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;


class CuratorRewardsController extends Controller
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
            $rewards = $this->getRewardsAll($acc);
            return $this->exportToExcel($rewards->toArray(), 'CuratorRewards', $acc);
        }

        /*Tracker::trackEvent(['event' => 'PowerUpDown']);*/


        $dataIn = $this->getRewardsIn($acc);
        $chartRewardsIn = $this->getChartRewardsIn($dataIn, $acc);
        //dd($wv_by_month);
        $key = $acc;
        $account = $acc;
        $date = Date::now();
        $form_action = 'CuratorRewardsController@showAll';
        return view(getenv('BCH_API') . '.trans.index-curator-rewards', [
            'account' => $acc,
            'acc' => $acc,
            'form_action' => $form_action,
            'date' => false,
            //'wv_by_month' => $wv_by_month,
            'month' => $month,
            'week' => true,
            'dataIn' => $dataIn,
            'chartRewardsIn' => $chartRewardsIn,
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
        $data['month'] = [];
        $data['total'] = [];
        $data['count'] = [];
        $data['allSP'] = 0;
        $collection = BchApi::getMongoDbCollection($acc);
        $data_by_monthes = $collection->aggregate([
            [
                '$match' => [
                    'type' => ['$eq' => 'curation_reward'],
                    'op.curator' => ['$eq' => $acc]
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
        //dump($res_arr);
        ksort($res_arr);
        foreach ($res_arr as $key => $item) {
            $fm = Date::parse($key . '01')->format('Y M');
            $data['total'][] = (BchApi::convertToSg($item['total']));
            $data['totalVests'][] = $item['total'];
            $data['count'][] = $item['count'];
            $data['month'][] = $fm;
            $data['date'][] = Date::parse($key . '01')->timestamp;
            $data['allSP'] = $data['allSP'] + BchApi::convertToSg($item['total']);
        }
        //dd($data);


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
                                text: 'Curator Rewards statistics for @" . $acc . "'
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


    public function getDataTableRewardsByMonth(Request $request, Builder $htmlBuilder)
    {
        return DataTables::collection($this->getRewardsByMonth($request->acc, $request->type,
            $request->date))->make(true);
    }


    public function getRewardsByMonth($acc, $type, $date)
    {
        /*$acc = $request->get('acc');
        $date  = $request->get('date');
        $type  = $request->get('type');*/

        //dump($acc,$date,$type);
        $date = Date::createFromTimestamp($date);
        $date_start = new MongoDB\BSON\UTCDateTime(($date->startOfMonth()));
        $date_end = new MongoDB\BSON\UTCDateTime(($date->endOfMonth()));
        //$date_end = $date->endOfMonth()->timestamp;

        $typeQ = '';
        $res_arr = [];
        $collection = BchApi::getMongoDbCollection($acc);
        if ($type == 'In') {
            $typeQ = '$eq';
        }
        if ($typeQ == '') {
            throw new Exception("Type is empty.");
        }
        $data_by_monthes = $collection->aggregate([
            [
                '$match' => [
                    'date' => ['$gte' => $date_start, '$lt' => $date_end],
                    //'date' => ['$lt'=>$date_end],
                    'type' => ['$eq' => 'curation_reward'],
                    'op.curator' => [$typeQ => $acc]
                ]
            ]
        ]);
        foreach ($data_by_monthes as $state) {

            if ($type == 'In') {
                $arr['author'] = $state['op'][1]['comment_author'];
            }

            $arr['permlink'] = $state['op'][1]['comment_permlink'];
            $arr['VESTS'] = $state['op'][1]['VESTS'];
            $arr['SP'] = BchApi::convertToSg($state['op'][1]['VESTS']);
            $arr['timestamp'] = $state['timestamp'];
            $res_arr[] = $arr;
        }
        //dump($res_arr);
        /*$grid = $this->getBenefactorInGrid($res_arr);
        echo $grid;*/
        return collect($res_arr);

    }

    /**
     * get all existed rewards for export
     * @param $acc
     * @return \Illuminate\Support\Collection
     */
    public function getRewardsAll($acc)
    {


        $res_arr = [];
        $collection = BchApi::getMongoDbCollection($acc);

        $data_by_monthes = $collection->aggregate([
            [
                '$match' => [
                    //'date' => ['$lt'=>$date_end],
                    'type' => ['$eq' => 'curation_reward'],
                    'op.curator' => ['$eq' => $acc],

                ]
            ],
            [
                '$sort' => [
                    'timestamp' => -1
                ]
            ]
        ]);
        foreach ($data_by_monthes as $state) {
            $arr['author'] = $state['op'][1]['comment_author'];
            $arr['permlink'] = $state['op'][1]['comment_permlink'];
            $arr['VESTS'] = $state['op'][1]['VESTS'];
            $arr['SP'] = BchApi::convertToSg($state['op'][1]['VESTS']);
            $arr['timestamp'] = Date::parse($state['timestamp'])->format('Y-m-d H:i:s');

            $res_arr[] = $arr;
        }
        //dump($res_arr);
        /*$grid = $this->getBenefactorInGrid($res_arr);
        echo $grid;*/
        return collect($res_arr);

    }

}