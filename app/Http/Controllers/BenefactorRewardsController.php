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
            $rewards = $this->getRewardsAll($acc,$request->type);
            return $this->exportToExcel($rewards->toArray(), 'BenefactorRewards', $acc);
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
            'dataIn' => $dataIn,
            'dataOut' => $dataOut,
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
        $data['month'] = [];
        $data['total'] = [];
        $data['count'] = [];
        $data['allSP'] = 0;
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
            $data['date'][] = Date::parse($key . '01')->timestamp;
            $data['allSP'] = $data['allSP'] + BchApi::convertToSg($item['total']);
        }


        return $data;
    }

    public function getRewardsOut($acc)
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
            $data['date'][] = Date::parse($key . '01')->timestamp;
            $data['allSP'] = $data['allSP'] + BchApi::convertToSg($item['total']);
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

    public function getDataTableRewardsByMonth(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return DataTables::collection($this->getRewardsByMonth($request->acc, $request->type,
                $request->date))->make(true);
        }
        $html = $htmlBuilder
            ->addColumn(['data' => 'author', 'name' => 'author', 'title' => 'author'])
            ->addColumn(['data' => 'permlink', 'name' => 'permlink', 'title' => 'permlink'])
            ->addColumn(['data' => 'VESTS', 'name' => 'VESTS', 'title' => 'VESTS'])
            ->addColumn(['data' => 'timestamp', 'name' => 'timestamp', 'title' => 'timestamp']);

        return view(getenv('BCH_API') . '.datatables.benefactor-rewards', compact('html'));
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
        if ($type == 'Out') {
            $typeQ = '$ne';
        }
        if ($typeQ == '') {
            throw new Exception("Type is empty.");
        }
        $data_by_monthes = $collection->aggregate([
            [
                '$match' => [
                    'date' => ['$gte' => $date_start, '$lt' => $date_end],
                    //'date' => ['$lt'=>$date_end],
                    'type' => ['$eq' => 'comment_benefactor_reward'],
                    'op.benefactor' => [$typeQ => $acc]
                ]
            ]
        ]);
        foreach ($data_by_monthes as $state) {

            if ($type == 'In') {
                $arr['author'] = $state['op'][1]['author'];
            }
            if ($type == 'Out') {
                $arr['author'] = $state['op'][1]['benefactor'];
            }

            $arr['permlink'] = $state['op'][1]['permlink'];
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
     * @param $type
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    public function getRewardsAll($acc, $type)
    {

        $typeQ = '';
        $res_arr = [];
        $collection = BchApi::getMongoDbCollection($acc);
        if ($type == 'In') {
            $typeQ = '$eq';
        }
        if ($type == 'Out') {
            $typeQ = '$ne';
        }
        if ($typeQ == '') {
            throw new Exception("Type is empty.");
        }
        $data_by_monthes = $collection->aggregate([
            [
                '$match' => [
                    //'date' => ['$lt'=>$date_end],
                    'type' => ['$eq' => 'comment_benefactor_reward'],
                    'op.benefactor' => [$typeQ => $acc]
                ]
            ],
            [
                '$sort' => [
                    'timestamp' => -1
                ]
            ]
        ]);
        foreach ($data_by_monthes as $state) {

            if ($type == 'In') {
                $arr['author'] = $state['op'][1]['author'];
            }
            if ($type == 'Out') {
                $arr['author'] = $state['op'][1]['benefactor'];
            }

            $arr['permlink'] = $state['op'][1]['permlink'];
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


    /**
     * @param $res_arr
     * @return Grid
     */
    private function getBenefactorInGrid($res_arr): Grid
    {
        $input = new InputSource($_GET);
        $provider = new ArrayDataProvider($res_arr);
        $grid = new Grid($provider, [
                new TableCaption('My Grid'),
                new Column('author'),
                new Column('permlink', "От"),
                new Column('VESTS'),
                new Column('timestamp'),
                //new PaginationControl($input->option('page', 1), 25),
                // 1 - default page, 5 -- page size
                //new PageSizeSelectControl($input->option('page_size', 5), [25, 50, 100, 250, 500, 1000, 5000, 10000]),
                // allows to select page size
                new PageTotalsRow([
                    'date' => function () {
                        return 'Page totals';
                    },
                    'sum' => PageTotalsRow::OPERATION_SUM,
                ])
            ]
        );
        $customization = new BootstrapStyling();
        $customization->apply($grid);
        $grid->getTileRow()->detach()->attachTo($grid->getTableHeading());
        return $grid;
    }
}