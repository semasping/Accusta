<?php

namespace App\Http\Controllers;


use App\semas\AdminNotify;
use App\semas\GolosApi;
use App\Swi\CurrencyOperations;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use DataTables;
use Jenssegers\Date\Date;

class TransAccController extends Controller
{
    public function index(Request $request, $_acc = '')
    {
        //dump($request->all());
        $acc = $request->get('acc');
        if (empty($acc)) {
            if (empty($_acc)) {
                return view('trans.notfound', ['account' => $_acc,
                    'form_action' => 'TransAccController@index',]);
            }
            $acc = $_acc;

        }
        $acc = str_replace('@', '', $acc);
        $acc = mb_strtolower($acc);
        $acc = trim($acc);
        $date = false;
        $vp_calc = false;
        if ($request->has('d_from')) {
            $date = $request->get('d_from');
        }
        if ($request->has('vp')) {
            $vp_calc = $request->get('vp');
        }
        if ($date==false){
            $date = Date::now()->subMonths(2)->startOfMonth();
        }
        //dd($date);
        try {
            if (!empty($acc)) {
                $textnotify = 'Старт Запроса на сбор #статситики для аккаунта #' . $acc;
                AdminNotify::send($textnotify);
                $max = GolosApi::getHistoryAccountLast($acc);

                //$trans = GolosApi::getData($acc, $max);
                //dd($trans);
                //$data = collect(GolosApi::getTransaction($acc, 'transfer'));

                $author_trans = collect(GolosApi::getTransaction($acc, 'author_reward'));//collect($trans['author_reward']);
                $author_trans = $author_trans->map(function ($item, $key) {
                    $author_data = $item;
                    $author_data['GBG'] = str_replace(' GBG', '', $item['sbd_payout']);
                    $author_data['GOLOS'] = str_replace(' GOLOS', '', $item['steem_payout']);
                    $author_data['GESTS'] = str_replace(' GESTS', '', $item['vesting_payout']);
                    return $author_data;
                });
                $curation_trans = collect(GolosApi::getTransaction($acc, 'curation_reward'));//collect($trans['curation_reward']);
                $curation_trans = $curation_trans->map(function ($item, $key) {
                    $curation_data = $item;
                    $curation_data['author'] = $item['comment_author'];
                    $curation_data['permlink'] = $item['comment_permlink'];
                    $curation_data['GESTS'] = str_replace(' GESTS', '', $item['reward']);
                    return $curation_data;
                });
                $posts_trans = collect(GolosApi::getTransaction($acc, 'comment'));//collect($trans['posts']);
                $posts_trans = $posts_trans->filter(function ($item) use ($acc){
                    if ($item['parent_author']==''&&$item['author']==$acc) return true;
                });
                dump($posts_trans);

                $posts_trans = $posts_trans->unique('permlink');
                dump($posts_trans);
                //$transfer_out_trans = collect([]);//collect(GolosApi::getTransaction($acc, 'transfer'));//collect($trans['transfer_out_data']);
                //dump($author_trans);

                if ($date) {
                    /*$author_trans = $author_trans->where(function ($item) use ($date) {
                        return Carbon::parse($item['timestamp'])->gte(Carbon::parse($date));
                    })->get();*/
                    $author_trans = $author_trans->where('timestamp', '>=', Carbon::parse($date)->toAtomString());
                    $curation_trans = $curation_trans->where('timestamp', '>=', Carbon::parse($date)->toAtomString());
                    $posts_trans = $posts_trans->where('timestamp', '>=', Carbon::parse($date)->toAtomString());
                    //$transfer_out_trans = $transfer_out_trans->where('timestamp', '>=', Carbon::parse($date)->toAtomString());
                }
                //dump($author_trans);

                //dump($posts_trans);
                if ($vp_calc) {
                    $author_trans = $author_trans->filter(function ($item) use ($posts_trans) {
                        //dump( substr($item['permlink'],0,3).':'.$item['permlink']);
                        if (substr($item['permlink'], 0, 3) == 're-') return $item;
                        if (array_key_exists($item['permlink'], $posts_trans->toArray())) return $item;
                    });
                }
                //dump($author_trans);
                //dump($author_trans);

                $all_author_rew['GESTS'] = $author_trans->sum('GESTS');
                $all_author_rew['GBG'] = $author_trans->sum('GBG');
                $all_author_rew['GOLOS'] = $author_trans->sum('GOLOS');

                $all_curation_rew['GESTS'] = $curation_trans->sum("GESTS");

                $all_gests = $all_author_rew['GESTS'] + $all_curation_rew['GESTS'];

                $posts_count_all = $posts_trans->count();

                $author_by_month = ($author_trans->sortByDesc('timestamp')->groupBy(function ($item) {
                    return Carbon::parse($item['timestamp'])->format('Y-m');
                }));
                foreach ($author_by_month as $key => $item) {
                    $month[$key] = $key;
                }

                $curator_by_month = ($curation_trans->sortByDesc('timestamp')->groupBy(function ($item) {
                    return Carbon::parse($item['timestamp'])->format('Y-m');
                }));
                foreach ($curator_by_month as $key => $item) {
                    $month[$key] = $key;
                }

                $posts_by_month = ($posts_trans->sortByDesc('timestamp')->groupBy(function ($item) {
                    return Carbon::parse($item['timestamp'])->format('Y-m');
                }));
                foreach ($posts_by_month as $key => $item) {
                    $month[$key] = $key;
                }

                /*$transfer_out_by_month = ($transfer_out_trans->sortByDesc('timestamp')->groupBy(function ($item) {
                    return Carbon::parse($item['timestamp'])->format('Y-m');
                }));
                foreach ($transfer_out_by_month as $key => $item) {
                    $month[$key] = $key;
                }*/
                krsort($month);
                //dump($trans['author_reward']);
                return view('trans.index', [
                    'account' => $acc,
                    'form_action' => 'TransAccController@index',
                    'author_by_month' => $author_by_month,
                    'curator_by_month' => $curator_by_month,
                    'posts_by_month' => $posts_by_month,
                    'posts_count_all' => $posts_count_all,
                    'all_gests' => $all_gests,
                    'all_author_rew' => $all_author_rew,
                    'date' => $date,
                    'vp_calc' => $vp_calc,
                    'month' => $month,
                    'transfer_out_by_month' => [],//$transfer_out_by_month,
                ]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            $textnotify = 'Ошибка запроса в сборе #статистики. Запрашиваемый аккаунт #' . $acc . $e->getMessage();;
            AdminNotify::send($textnotify);
            GolosApi::disconnect();
            return view('trans.notfound', ['account' => $acc,
                'form_action' => 'TransAccController@index',]);
        }
    }

    public function indexByWeek(Request $request, $_acc = '')
    {
        //dump($request->all());
        $acc = $request->get('acc');
        if (empty($acc)) {
            if (empty($_acc)) {
                return view('trans.notfound', ['account' => $_acc,
                    'form_action' => 'TransAccController@indexByWeek',]);
            }
            $acc = $_acc;

        }
        $acc = str_replace('@', '', $acc);
        $acc = mb_strtolower($acc);
        $acc = trim($acc);
        $date = false;
        $vp_calc = false;
        if ($request->has('d_from')) {
            $date = $request->get('d_from');
        }
        if ($request->has('vp')) {
            $vp_calc = $request->get('vp');
        }
        try {
            if (!empty($acc)) {
                $textnotify = 'Старт Запроса на сбор #статситики по неделям для аккаунта #' . $acc;
                AdminNotify::send($textnotify);
                $max = GolosApi::getHistoryAccountLast($acc);

                $trans = GolosApi::getData($acc, $max);
                $author_trans = collect($trans['author_reward']);
                $curation_trans = collect($trans['curation_reward']);
                $posts_trans = collect($trans['posts']);
                $transfer_out_trans = collect($trans['transfer_out_data']);
                //dump($author_trans);

                if ($date) {
                    /*$author_trans = $author_trans->where(function ($item) use ($date) {
                        return Carbon::parse($item['timestamp'])->gte(Carbon::parse($date));
                    })->get();*/
                    $author_trans = $author_trans->where('timestamp', '>=', Carbon::parse($date)->toAtomString());
                    $curation_trans = $curation_trans->where('timestamp', '>=', Carbon::parse($date)->toAtomString());
                    $posts_trans = $posts_trans->where('timestamp', '>=', Carbon::parse($date)->toAtomString());
                    $transfer_out_trans = $transfer_out_trans->where('timestamp', '>=', Carbon::parse($date)->toAtomString());
                }
                //dump($author_trans);

                //dump($posts_trans);
                if ($vp_calc) {
                    $author_trans = $author_trans->filter(function ($item) use ($posts_trans) {
                        //dump( substr($item['permlink'],0,3).':'.$item['permlink']);
                        if (substr($item['permlink'], 0, 3) == 're-') return $item;
                        if (array_key_exists($item['permlink'], $posts_trans->toArray())) return $item;
                    });
                }
                //dump($author_trans);
                //dump($author_trans);

                $all_author_rew['GESTS'] = $author_trans->sum('GESTS');
                $all_author_rew['GBG'] = $author_trans->sum('GBG');
                $all_author_rew['GOLOS'] = $author_trans->sum('GOLOS');

                $all_curation_rew['GESTS'] = $curation_trans->sum("GESTS");

                $all_gests = $all_author_rew['GESTS'] + $all_curation_rew['GESTS'];

                $posts_count_all = $posts_trans->count();

                $author_by_month = ($author_trans->sortByDesc('timestamp')->groupBy(function ($item) {
                    return Carbon::parse($item['timestamp'])->format('Y\WW');
                }));
                foreach ($author_by_month as $key => $item) {
                    //$fm = Date::parse('2017W'.$key)->format('Y-m');
                    $month[$key] = $key;
                }

                $curator_by_month = ($curation_trans->sortByDesc('timestamp')->groupBy(function ($item) {
                    return Carbon::parse($item['timestamp'])->format('Y\WW');
                }));
                foreach ($curator_by_month as $key => $item) {
                    $month[$key] = $key;
                }

                $posts_by_month = ($posts_trans->sortByDesc('timestamp')->groupBy(function ($item) {
                    return Carbon::parse($item['timestamp'])->format('Y\WW');
                }));
                foreach ($posts_by_month as $key => $item) {
                    $month[$key] = $key;
                }

                $transfer_out_by_month = ($transfer_out_trans->sortByDesc('timestamp')->groupBy(function ($item) {
                    return Carbon::parse($item['timestamp'])->format('Y\WW');
                }));
                foreach ($transfer_out_by_month as $key => $item) {
                    $month[$key] = $key;
                }
                krsort($month);
                //dump($trans['author_reward']);
                //if (!$date) $date =
                //dd($curator_by_month['2017W48']);
                return view('trans.index-by-week', [
                    'account' => $acc,
                    'form_action' => 'TransAccController@indexByWeek',
                    'author_by_month' => $author_by_month,
                    'curator_by_month' => $curator_by_month,
                    'posts_by_month' => $posts_by_month,
                    'posts_count_all' => $posts_count_all,
                    'all_gests' => $all_gests,
                    'all_author_rew' => $all_author_rew,
                    'date' => $date,
                    'vp_calc' => $vp_calc,
                    'month' => $month,
                    'transfer_out_by_month' => $transfer_out_by_month,
                    'week' => true,
                ]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            $textnotify = 'Ошибка запроса в сборе #статистики по неделям. Запрашиваемый аккаунт #' . $acc . $e->getMessage();;
            AdminNotify::send($textnotify);
            GolosApi::disconnect();
            return view('trans.notfound', ['account' => $acc,
                'form_action' => 'TransAccController@indexByWeek',]);
        }
    }

    public function data($acc)
    {
        //dump($acc);
        if (!empty($acc)) {
            $textnotify = 'Старт Запроса на подсчет транзакций для аккаунта ' . $acc;
            AdminNotify::send($textnotify);
            $max = GolosApi::getHistoryAccountLast($acc);

            $trans = collect($this->getData($acc, $max));
            return DataTables::of($trans)->toJson();
        }
    }

    public static function disp($tr)
    {
        dump($tr);
    }

    public function getData($acc, $max)
    {
        return Cache::remember('tr' . $acc . $max, 10, function () use ($max, $acc) {
            $history = [];
            $data = [];
            $author_data = [];
            $kur_data = [];
            $post_data = [];
            $transfer_out_data = [];
            $qq = 0;
            $h = 0;
            $i = 2000;
            $limit = 2000;
            if ($i > $max) {
                $i = $max;
                $limit = $max;
            }
            while ($i <= $max) {

                $his = GolosApi::getHistoryAccount($acc, $i, $limit);
                //dump($his);

                foreach ($his as $item) {
                    if (isset($item[1]['op'])) {
                        //dump($item);
                        $type_op = $item[1]['op'][0];


                        if (isset($item[1]['op']) && $item[1]['op'][0] == 'account_create') {
                            //AdminNotify::send(print_r($his, true));
                            //dump($item);
                            //    $reward = $this->processReward($item);
                            $block = $item[1]['block'];
                            $account_create['block'] = $item[1]['block'];
                            $account_create['timestamp'] = $item[1]['timestamp'];
                            //->delay($date);
                        }


                        if (isset($item[1]['op']) && $item[1]['op'][0] == 'author_reward') {
                            //AdminNotify::send(print_r($his, true));
                            //dump($item);
                            //    $reward = $this->processReward($item);
                            $block = $item[1]['block'];
                            $permlink = $item[1]['op'][1]['permlink'];
                            $author_data[$h]['block'] = $item[1]['block'];
                            $author_data[$h]['timestamp'] = $item[1]['timestamp'];
                            $author_data[$h]['permlink'] = $item[1]['op'][1]['permlink'];
                            $author_data[$h]['GBG'] = str_replace(' GBG', '', $item[1]['op'][1]['sbd_payout']);
                            $author_data[$h]['GOLOS'] = str_replace(' GOLOS', '', $item[1]['op'][1]['steem_payout']);
                            $author_data[$h]['GESTS'] = str_replace(' GESTS', '', $item[1]['op'][1]['vesting_payout']);
                            //->delay($date);
                        }

                        if (isset($item[1]['op']) && $item[1]['op'][0] == 'curation_reward') {
                            //AdminNotify::send(print_r($his, true));
                            //dump($item);
                            //    $reward = $this->processReward($item);
                            $block = $item[1]['block'];
                            //$permlink = $item[1]['op'][1]['permlink'];
                            $kur_data[$h]['block'] = $item[1]['block'];
                            $kur_data[$h]['timestamp'] = $item[1]['timestamp'];
                            //$kur_data[$h]['permlink'] = $item[1]['op'][1]['permlink'];
                            $kur_data[$h]['GESTS'] = str_replace(' GESTS', '', $item[1]['op'][1]['reward']);
                            //->delay($date);
                        }

                        if (isset($item[1]['op']) && $item[1]['op'][0] == 'comment' && $item[1]['op'][1]['parent_author'] == '') {
                            //AdminNotify::send(print_r($his, true));
                            //dump($item);
                            //    $reward = $this->processReward($item);
                            $block = $item[1]['block'];
                            //$permlink = $item[1]['op'][1]['permlink'];
                            $post_data[$h]['block'] = $item[1]['block'];
                            $post_data[$h]['timestamp'] = $item[1]['timestamp'];
                            $post_data[$h]['permlink'] = $item[1]['op'][1]['permlink'];
                            //->delay($date);
                        }

                        if (isset($item[1]['op']) && $item[1]['op'][0] == 'transfer' && $item[1]['op'][1]['from'] == $acc) {
                            //AdminNotify::send(print_r($his, true));
                            //dump($item);
                            //    $reward = $this->processReward($item);
                            $block = $item[1]['block'];
                            //$permlink = $item[1]['op'][1]['permlink'];
                            $transfer_out_data[$h]['block'] = $item[1]['block'];
                            $transfer_out_data[$h]['timestamp'] = $item[1]['timestamp'];
                            //$post_data[$h]['permlink'] = $item[1]['op'][1]['permlink'];
                            $transfer_out_data[$h]['to'] = $item[1]['op'][1]['to'];
                            $transfer_out_data[$h]['amount'] = $item[1]['op'][1]['amount'];
                            $transfer_out_data[$h]['memo'] = $item[1]['op'][1]['memo'];
                            //->delay($date);
                        }
                        $history[$type_op][] = $item;

                    } else {
                        //echo 1;
                        //dump($item);
                        //AdminNotify::send(print_r($item,true));
                    }
                    $h++;
                }
                unset($his);
                $i = $i + 2000;
                if ($i > $max) {
                    //AdminNotify::send('i' . $i);
                    $i = $i - 2000;
                    $limit = $max - $i;
                    $i = $max;
                }
                if ($limit == 0) {
                    //AdminNotify::send('limit =0 exit');
                    break;
                }
                $qq++;
                if ($qq > 10000) {
                    AdminNotify::send('$qq > 10000 exit');
                    break;
                }
            }
            $data['account_create'] = $account_create;
            $data['author_reward'] = $author_data;
            $data['curation_reward'] = $kur_data;
            $data['posts'] = $post_data;
            $data['transfer_out_data'] = $transfer_out_data;

            /*            dump(array_keys($history));
                        dump($history['custom_json']);
                        dump($history['comment_options']);
                        dump($history['interest']);
                        dump($history['delete_comment']);*/
            for ($i = 0; $i < 100; $i++) {
            }
            //dump($history['transfer'][$i]);
            //dump($history['account_create']);
            return $data;
        });
    }

    public function indexSg(Request $request, $_acc = '')
    {
        $acc = $request->get('acc');
        $month = [];
        if (empty($acc)) {
            if (empty($_acc)) {
                return view('trans.notfound', ['account' => $_acc,
                    'form_action' => 'TransAccController@indexSg',]);
            }
            $acc = $_acc;

        }
        $acc = str_replace('@', '', $acc);
        $acc = mb_strtolower($acc);
        $acc = trim($acc);
        $date = Date::now()->subDays(30);
        $vp_calc = false;

        if ($request->has('d_from')) {
            $date = $request->get('d_from');
        }

        try {

            if (!empty($acc)) {
                $textnotify = 'Старт Запроса на сбор #статситики по #sg для аккаунта #' . $acc;
                AdminNotify::send($textnotify);

                $data_withdraw_vesting = collect(GolosApi::getTransaction($acc, 'fill_vesting_withdraw'));

                $data_withdraw_vesting = $data_withdraw_vesting->where('timestamp', '>=', Carbon::parse($date)->toAtomString());
                //dump($data_withdraw_vesting);
                /*$data_withdraw_vesting = $data_withdraw_vesting->map(function ($item) {
                    $ni = $item;
                    $ni['date'] = Date::parse($ni['timestamp'])->format('Y.m.d H:i');
                    $ni['GESTS'] = str_replace(' GESTS', '', $item['withdrawn']);
                    return $ni;
                });*/

                $wv_by_month = ($data_withdraw_vesting->sortByDesc('timestamp')->groupBy(function ($item) {
                    return Carbon::parse($item['timestamp'])->format('Y\WW');
                }));
                foreach ($wv_by_month as $key => $item) {
                    //$fm = Date::parse('2017W'.$key)->format('Y-m');
                    $month[$key] = $key;
                }
                //dump($wv_by_month,$month);
                $wv_by_month = $wv_by_month->toArray();
                if (!is_array($wv_by_month)){
                    $wv_by_month = [];
                }
                krsort($wv_by_month);
                //dump($wv_by_month,$month);
                return view('trans.index-sg', [
                    'account' => $acc,
                    'acc' => $acc,
                    'form_action' => 'TransAccController@indexSg',
                    'date' => $date,
                    'wv_by_month'=>$wv_by_month,
                    'month' => $month,
                    'week' => true,
                ]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            $textnotify = 'Ошибка запроса в сборе #статистики по #sg. Запрашиваемый аккаунт #' . $acc . $e->getMessage();;
            AdminNotify::send($textnotify);
            GolosApi::disconnect();
            return view('trans.notfound', ['account' => $acc,
                'form_action' => 'TransAccController@indexSg',]);
        }
    }
}



[
    "vote" => "",
    "transfer" => "",
    "comment" => "",
    "transfer_to_vesting" => "",
    "curation_reward" => "",
    "author_reward" => "",
    "account_update" => "",
    "account_create" => "",
    "interest" => "",
    "custom_json" => "",
    "delete_comment" => "",
    "transfer_to_savings" => "",
    "convert" => "",
    "account_witness_vote" => "",
    "fill_convert_request" => "",
    "comment_options" => "",
    "withdraw_vesting" => "",
    "fill_vesting_withdraw" => ""
];
