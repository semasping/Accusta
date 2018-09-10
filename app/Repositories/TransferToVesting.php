<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 08.09.2018
 * Time: 1:31
 */

namespace App\Repositories;


use App\semas\BchApi;
use Jenssegers\Date\Date;
use MongoDB;


class TransferToVesting
{
    public function __construct()
    {

    }

    public static function get($acc, $from = '', $to = '')
    {


        $date_start = new MongoDB\BSON\UTCDateTime(($from));
        $date_end = new MongoDB\BSON\UTCDateTime(($to));
        $collection = BchApi::getMongoDbCollection($acc);
        $data_by_monthes = $collection->aggregate([
            [
                '$match' => [
                    'type' => ['$eq' => 'transfer_to_vesting'],
                    'op.to' => ['$eq' => $acc],
                    'date' => ['$gte' => $date_start, '$lt' => $date_end],
                ]
            ],
            /*['$unwind' => '$op'],
            [
                '$group' => [
                    '_id' => ['date' => ['M' => ['$month' => '$date'], 'Y' => ['$year' => '$date'],]],
                    //'total' => ['$sum' => '$op.VESTS'],
                    'count' => ['$sum' => 1]
                ]
            ],*/
            [
                '$sort' => [
                    'timestamp' => -1
                ]
            ]
        ]);
        foreach ($data_by_monthes as $state) {
            //dump($state);
            $arr['from'] = $state['op'][1]['from'];
            $arr['to'] = $state['op'][1]['to'];
            $arr['amount'] = $state['op'][1]['amount'];
            $arr['date'] = Date::parse($state['timestamp'])->format('Y F d h:i');
            $res_arr[]= $arr;
            /*$date = Date::parse('01.' . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);


            $arr['total'] = $state['total'];
            $arr['count'] = $state['count'];
            $arr['date'] = $date->endOfMonth();
            $res_arr[$date->format('Ym')] = $arr;*/

        }
        //dump($res_arr);
        /*//
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
        //dd($data);*/


        return $res_arr;
    }
}