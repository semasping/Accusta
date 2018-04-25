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
use PragmaRX\Tracker\Vendor\Laravel\Facade as Tracker;


class PowerUpDownController extends Controller
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
        Tracker::trackEvent(['event' => 'PowerUpDown for @'.$acc]);
        Tracker::trackEvent(['event' => '@'.$acc]);

        $collection = BchApi::getMongoDbCollection($acc);
        //$data = $collection->find(['op'=>'producer_reward']);
        $data_by_monthes = $collection->aggregate([
            //['$group'=>['_id'=>['date'=>['M'=>['$month'=>'$date'], 'Y' => ['$year' => '$date']]]]],
            ['$match' => ['type' => ['$eq' => 'fill_vesting_withdraw']]],

            //['$limit'=> 60],
            //['$unwind' => '$op'],
             //['$group' => ['_id' => ['date' => ['M' => ['$month' => '$date'], 'Y' => ['$year' => '$date'],]], 'total' => ['$sum' => '$op.VESTS'], 'total2' => ['$sum' => '$op.STEEM']]],
            //['$sort'=>['$date']]
        ]);
//dd($rewards_by_monthes);
        foreach ($data_by_monthes as $state) {
            //dd($state);
            //$date = Date::parse($state['_id']['date']['d'] . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);
            //$arr['date'] = Date::parse($state['timestamp'])->format('Y F d h:i');
            $arr['timestamp'] = $state['timestamp'];
            $arr['from_account'] = $state['op']['1']['from_account'];
            $arr['to_account'] = $state['op']['1']['to_account'];
            $arr['withdrawn'] = $state['op']['1']['withdrawn'];
            $arr['deposited'] = $state['op']['1']['deposited'];
            $res_arr[Date::parse($state['timestamp'])->format('Y\WW')] = $arr;
        }
        if ($request->csv) {
            Tracker::trackEvent(['event' => 'CSV PowerUpDown']);

            return $this->exportToExcel($res_arr, 'GolosPowerDown', $acc);
        }

        Tracker::trackEvent(['event' => 'PowerUpDown']);

        $author = collect($res_arr)->sortByDesc('timestamp');

        foreach ($res_arr as $key => $item) {
            //$fm = Date::parse('2017W'.$key)->format('Y-m');
            $month[$key] = $key;
        }
        //dump($wv_by_month,$month);
        $wv_by_month = $author->toArray();
        if (!is_array($wv_by_month)) {
            $wv_by_month = [];
        }
        krsort($wv_by_month);
        //dd($wv_by_month);
        $key = $acc;
        $account = $acc;
        $date = Date::now();
        $form_action = 'PowerUpDownController@showAll';
        return view(getenv('BCH_API') . '.trans.index-sg', [
            'account' => $acc,
            'acc' => $acc,
            'form_action' => $form_action,
            'date' => false,
            'wv_by_month' => $wv_by_month,
            'month' => $month,
            'week' => true,
        ]);
        //return view(env('BCH_API').'.rewards', compact('author', 'key','account','date','form_action'));
        //dump($res_arr);
    }
}