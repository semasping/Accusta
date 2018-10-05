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

        $res_arr = [];

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
            [
                '$sort' => [
                    'timestamp' => -1
                ]
            ]
        ]);

        foreach ($data_by_monthes as $state) {
            $arr['from'] = $state['op'][1]['from'];
            $arr['to'] = $state['op'][1]['to'];
            $arr['amount'] = $state['op'][1]['amount'];
            $arr['date'] = Date::parse($state['timestamp'])->format('Y F d h:i');
            $res_arr[]= $arr;


        }

        return $res_arr;
    }
}