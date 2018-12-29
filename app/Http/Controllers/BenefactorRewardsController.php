<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 25.03.2018
 * Time: 16:23
 */

namespace App\Http\Controllers;


use App\semas\BchApi;
use App\Services\Charts;
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

        if ($request->csv) {
            /*Tracker::trackEvent(['event' => 'CSV PowerUpDown']);*/
            $rewards = $this->getRewardsAll($acc,$request->get('type'));
            return $this->exportToExcel($rewards->toArray(), 'BenefactorRewards', $acc);
        }

        $dataIn = $this->getRewardsIn($acc);
        $chartRewardsIn = $this->getChartRewardsIn($dataIn, $acc);
        $dataOut = $this->getRewardsOut($acc);
        $chartRewardsOut = $this->getChartRewardsOut($dataOut, $acc);
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
            $date = Date::parse('01.' . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);
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
            $date = Date::parse('01.' . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);
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
        $labels = [
            'dataset1' => __(getenv('BCH_API') .'.shares'),
            'dataset2' => __(getenv('BCH_API') .'.count_rewards'),
            'title' => __(getenv('BCH_API') .'.title_benefactor_in', ['acc', $acc]),
            'name' => 'lineChartIn'
        ];

        return Charts::getChartRewards($data,$acc,$labels);

    }

    private function getChartRewardsOut($data, $acc)
    {
        $labels = [
            'dataset1' => __(getenv('BCH_API') .'.shares'),
            'dataset2' => __(getenv('BCH_API') .'.count_rewards'),
            'title' => __(getenv('BCH_API') .'.title_benefactor_out'),
            'name' => 'lineChartOut'

        ];

        return Charts::getChartRewards($data,$acc,$labels);

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
        if ($type == 'in') {
            $typeQ = '$eq';
        }
        if ($type == 'out') {
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