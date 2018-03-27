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


class AuthorRewardsController extends Controller
{
    public function showAll(Request $request, $_acc = '')
    {
        $res_arr = [];
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
        $collection = BchApi::getMongoDbCollection($acc);
        //$data = $collection->find(['op'=>'producer_reward']);
        $rewards_by_monthes = $collection->aggregate([
            //['$group'=>['_id'=>['date'=>['M'=>['$month'=>'$date'], 'Y' => ['$year' => '$date']]]]],
            ['$match' => ['type' => ['$eq' => 'author_reward']]],

            //['$limit'=> 60],
            //['$unwind' => '$op'],
             //['$group' => ['_id' => ['date' => ['M' => ['$month' => '$date'], 'Y' => ['$year' => '$date'],]], 'total' => ['$sum' => '$op.VESTS'], 'total2' => ['$sum' => '$op.STEEM']]],
            //['$sort'=>['$date']]
        ]);
//dd($rewards_by_monthes);
        foreach ($rewards_by_monthes as $state) {
  //          dd($state);
            $date = Date::parse($state['_id']['date']['d'] . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);
            //$arr['date'] = Date::parse($state['timestamp'])->format('Y F d h:i');
            $arr['timestamp'] = $state['timestamp'];
            $arr['permlink'] = $state['op']['1']['permlink'];
            //$arr['sbd_payout'] = $state['op']['1']['sbd_payout'];
            //$arr['steem_payout'] = $state['op']['1']['steem_payout'];
            //$arr['vesting_payout'] = $state['op']['1']['vesting_payout'];
            //$arr['VESTS'] = $state['op']['1']['VESTS'];
            $arr['GESTS'] = $state['op']['1']['VESTS'];
            $arr['GOLOS'] = $state['op']['1']['STEEM'];
            $arr['GBG'] = $state['op']['1']['SBD'];
            $arr['SP'] = BchApi::convertToSg($arr['GESTS']);
            $res_arr[] = $arr;
        }
        $author = collect($res_arr);
        $key = $acc;
        $account = $acc;
        $date = Date::now();
        $form_action = 'AuthorRewardsController@showAll';
        return view(env('BCH_API').'.rewards', compact('author', 'key','account','date','form_action'));
        //dump($res_arr);
    }
}