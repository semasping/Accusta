<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 28.08.2018
 * Time: 17:26
 */

namespace App\Http\Controllers;

use App\semas\BchApi;
use App\semas\SteemitApi;
use App\Widgets\WitnessRewards;
use Exception;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use MongoDB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class RewardsPageController extends Controller
{
    public function showAll(Request $request, $_acc = ''){
        $res_arr = [];
        $month = [];
        $sums = 0;
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

        $account_data = SteemitApi::getAccountFull($acc);
        $vs = $account_data[0]['vesting_shares'];
        $sp = SteemitApi::convertToSg((int)$vs);


        $authorRewards = (new AuthorRewardsController())->getRewardsIn($acc);
        $sp_authorRewards = $authorRewards['allSP'];

        $curatorRewards = (new CuratorRewardsController())->getRewardsIn($acc);
        $sp_curatorRewards = $curatorRewards['allSP'];

        $benefactorRewards = (new BenefactorRewardsController())->getRewardsIn($acc);
        $sp_benefactorRewards = $benefactorRewards['allSP'];

        $witnessRewards = (new WitnessRewards())->getRewardsIn($acc);
        $sp_witnessRewards = $witnessRewards['allSP']['all'];

        $sums = $sp_authorRewards+$sp_curatorRewards+$sp_benefactorRewards+$sp_witnessRewards;

        $sp_other = $sp - $sums;

        //dump($sp_authorRewards,$sp_curatorRewards,$sp_benefactorRewards,$sp_witnessRewards,$sums, $sp_other);

        $chartsData=[$sp_authorRewards,$sp_curatorRewards,$sp_benefactorRewards,$sp_witnessRewards,$sp_other];

        $chartjs = $this->chart($chartsData);
        $key = $acc;
        $account = $acc;
        $date = Date::now();
        $form_action = 'RewardsPageController@showAll';
        return view(getenv('BCH_API') . '.trans.index-rewards', [
            'account' => $acc,
            'acc' => $acc,
            'form_action' => $form_action,
            'date' => false,
            'chartjs' => $chartjs,

            //'wv_by_month' => $wv_by_month,
            /*'month' => $month,
            'week' => true,
            'dataIn' => $dataIn,
            'chartRewardsSP' => $chartRewardsSP,
            'chartRewardsSTEEM' => $chartRewardsSTEEM,
            'chartRewardsSBD' => $chartRewardsSBD,*/
        ]);
    }

    public function chart($data)
    {
        $chartjs = app()->chartjs
            ->name('pieChartTest')
            ->type('pie')
            ->size(['width' => 400, 'height' => 200])
            ->labels(['Author Rewards', 'Curator Rewards','Benefactor Rewards','Production Rewards','Other'])
            ->datasets([
                [
                    'backgroundColor' => ['#FF6384', '#36A2EB','#86BF68 ','#FF9F63','#aaa'],
                    'hoverBackgroundColor' => ['#FF6384', '#36A2EB','#86BF68','#FF9F63','#aaa'],
                    'data' => $data
                ]
            ])
            ->options([]);

        return $chartjs;
    }
}