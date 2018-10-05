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


        $collection = BchApi::getMongoDbCollection($acc);
        $data_by_monthes = $collection->aggregate([
            ['$match' => ['type' => ['$eq' => 'fill_vesting_withdraw']]],
        ]);

        foreach ($data_by_monthes as $state) {

            $arr['timestamp'] = $state['timestamp'];
            $arr['from_account'] = $state['op']['1']['from_account'];
            $arr['to_account'] = $state['op']['1']['to_account'];
            $arr['withdrawn'] = $state['op']['1']['withdrawn'];
            $arr['deposited'] = $state['op']['1']['deposited'];
            $res_arr[Date::parse($state['timestamp'])->format('Y\WW')] = $arr;
        }
        $author = collect($res_arr)->sortByDesc('timestamp');

        if ($request->csv) {

            return $this->exportToExcel($author->toArray(), 'GolosPowerDown', $acc);
        }



        foreach ($res_arr as $key => $item) {
            $month[$key] = $key;
        }
        $wv_by_month = $author->toArray();
        if (!is_array($wv_by_month)) {
            $wv_by_month = [];
        }
        krsort($wv_by_month);
        krsort($month);


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
    }

    public function exportToExcel($data, $type, $acc)
    {
        Excel::create($type . '_' . $acc, function ($excel) use ($data, $type, $acc) {

            $excel->sheet($type, function ($sheet) use ($data) {

                $sheet->fromArray($data);

            });

        })->download('csv');
    }
}