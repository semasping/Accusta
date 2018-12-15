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
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;


class AuthorRewardsController extends Controller
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
            $rewards = $this->getRewardsAll($acc);
            return $this->exportToExcel($rewards->toArray(), 'AuthorRewards', $acc);
        }

        $dataIn = $this->getRewardsIn($acc);
        $chartRewardsSP = $this->getChartRewardsSP($dataIn, $acc);
        $chartRewardsSTEEM = $this->getChartRewardsSTEEM($dataIn, $acc);
        $chartRewardsSBD = $this->getChartRewardsSBD($dataIn, $acc);
        $form_action = 'AuthorRewardsController@showAll';

        return view(getenv('BCH_API') . '.trans.index-author-rewards', [
            'account' => $acc,
            'acc' => $acc,
            'form_action' => $form_action,
            'date' => false,
            //'wv_by_month' => $wv_by_month,
            'month' => $month,
            'week' => true,
            'dataIn' => $dataIn,
            'chartRewardsSP' => $chartRewardsSP,
            'chartRewardsSTEEM' => $chartRewardsSTEEM,
            'chartRewardsSBD' => $chartRewardsSBD,
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
        $data['total_steem'] = [];
        $data['total_sbd'] = [];
        $data['count'] = [];
        $data['allSBD'] = 0;
        $data['allSTEEM'] = 0;
        $data['allSP'] = 0;
        $collection = BchApi::getMongoDbCollection($acc);
        $data_by_monthes = $collection->aggregate([
            [
                '$match' => [
                    'type' => ['$eq' => 'author_reward'],
                    'op.author' => ['$eq' => $acc]
                ]
            ],
            ['$unwind' => '$op'],
            [
                '$match' => ['op.VESTS'=>['$gte'=>0]],
            ],
            [
                '$group' => [
                    '_id' => ['date' => ['M' => ['$month' => '$date'], 'Y' => ['$year' => '$date'],]],
                    'total' => ['$sum' => '$op.VESTS'],
                    'total_steem' => ['$sum' => '$op.STEEM'],
                    'total_sbd' => ['$sum' => '$op.SBD'],
                    'count' => ['$sum' => 1]
                ]
            ],
            [
                '$sort' => [
                    'timestamp' => -1
                ]
            ]
        ]);
        foreach ($data_by_monthes as $state) {
            $date = Date::parse('01.' . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);
            $arr['total'] = $state['total'];
            $arr['total_steem'] = $state['total_steem'];
            $arr['total_sbd'] = $state['total_sbd'];
            $arr['count'] = $state['count'];
            $arr['date'] = $date->endOfMonth();
            $res_arr[$date->format('Ym')] = $arr;
        }

        ksort($res_arr);
        foreach ($res_arr as $key => $item) {
            $fm = Date::parse($key . '01')->format('Y M');
            $data['total'][] = (BchApi::convertToSg($item['total']));
            $data['total_steem'][] = (($item['total_steem']));
            $data['total_sbd'][] = (($item['total_sbd']));
            $data['totalVests'][] = $item['total'];
            $data['count'][] = $item['count'];
            $data['month'][] = $fm;
            $data['date'][] = Date::parse($key . '01')->timestamp;
            $data['allSBD'] = $data['allSBD'] + $item['total_sbd'];
            $data['allSTEEM'] = $data['allSTEEM'] + $item['total_steem'];
            $data['allSP'] = $data['allSP'] + BchApi::convertToSg($item['total']);
        }

        return $data;
    }


    private function getChartRewardsSP($data, $acc)
    {
        $labels = [
            'dataset1' => __(getenv('BCH_API') .'.shares'),
            'dataset2' => __(getenv('BCH_API') .'.count_rewards'),
            'title' => __(getenv('BCH_API') .'.title_author_rewards_shares'),
            'name' => 'spChart'
        ];

        return Charts::getChartRewards($data,$acc,$labels);
    }

    private function getChartRewardsSBD($data, $acc)
    {
        $labels = [
            'dataset1' => __(getenv('BCH_API') .'.peg'),
            'dataset2' => __(getenv('BCH_API') .'.count_rewards'),
            'title' => __(getenv('BCH_API') .'.title_author_rewards_peg'),
            'name' => 'sbdChart'
        ];

        return Charts::getChartRewards($data,$acc,$labels);


    }

    private function getChartRewardsSTEEM($data, $acc)
    {
        $labels = [
            'dataset1' => __(getenv('BCH_API') .'.token'),
            'dataset2' => __(getenv('BCH_API') .'.count_rewards'),
            'title' => __(getenv('BCH_API') .'.title_author_rewards_token'),
            'name' => 'steemChart'
        ];

        return Charts::getChartRewards($data,$acc,$labels);

    }


    public function getDataTableRewardsByMonth(Request $request, Builder $htmlBuilder)
    {
        return DataTables::collection($this->getRewardsByMonth($request->acc, $request->type,
            $request->date))->make(true);
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
        if ($typeQ == '') {
            throw new Exception("Type is empty.");
        }
        $data_by_monthes = $collection->aggregate([
            [
                '$match' => [
                    'date' => ['$gte' => $date_start, '$lt' => $date_end],
                    //'date' => ['$lt'=>$date_end],
                    'type' => ['$eq' => 'author_reward'],
                    'op.author' => [$typeQ => $acc]
                ]
            ],
            [
                '$sort' => [
                    'timestamp' => -1
                ]
            ]
        ]);
        foreach ($data_by_monthes as $state) {
            $arr['SBD'] = 0;
            $arr['STEEM'] = 0;
            $arr['VESTS'] = 0;
            $arr['SP'] = 0;
            if ($type == 'In') {
                $arr['author'] = $state['op'][1]['author'];
            }

            $arr['permlink'] = $state['op'][1]['permlink'];
            $arr['STEEM'] = $state['op'][1]['STEEM'];
            if (isset($state['op'][1]['SBD'])) {
                $arr['SBD'] = $state['op'][1]['SBD'];
            }
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
                    'type' => ['$eq' => 'author_reward'],
                    'op.author' => ['$eq' => $acc],

                ]
            ],
            [
                '$sort' => [
                    'timestamp' => -1
                ]
            ]
        ]);
        foreach ($data_by_monthes as $state) {
            $arr['SBD'] = 0;
            $arr['STEEM'] = 0;
            $arr['VESTS'] = 0;
            $arr['SP'] = 0;

            $arr['author'] = $state['op'][1]['author'];
            $arr['permlink'] = $state['op'][1]['permlink'];
            $arr['STEEM'] = $state['op'][1]['STEEM'];
            $arr['SBD'] = $state['op'][1]['SBD'];
            $arr['VESTS'] = $state['op'][1]['VESTS'];
            $arr['SP'] = BchApi::convertToSg($state['op'][1]['VESTS']);
            $arr['timestamp'] = $state['timestamp'];
            $res_arr[] = $arr;
        }

        return collect($res_arr);
    }
}