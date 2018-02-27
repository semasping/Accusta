<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 27.02.2018
 * Time: 1:16
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jenssegers\Date\Date;


class WitnessPageController
{
    function show(Request $request, $_acc = '')
    {
        $acc = $request->get('acc');
        if (empty($acc)) {
            if (empty($_acc)) {
                return view(getenv('BCH_API') . '.trans.notfound', ['account' => $_acc,
                    'form_action' => 'TransAccController@index',]);
            }
            $acc = $_acc;

        }
        $acc = str_replace('@', '', $acc);
        $acc = mb_strtolower($acc);
        $acc = trim($acc);


        $date = false;
        if ($request->has('d_from')) {
            $date = $request->get('d_from');
        }
        if ($date == false) {
            $date = Date::now()->subMonths(2)->startOfMonth();
        }

        return view(getenv('BCH_API') . '.witness', ['account' => $acc, 'form_action' => 'WitnessPageController@show', 'date' => $date]);
    }
}