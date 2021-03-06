<?php

namespace App\Widgets;

use App\Repositories\FullCurrentDataOfAccount;
use App\semas\BchApi;
use Arrilot\Widgets\AbstractWidget;
use Jenssegers\Date\Date;
use MongoDB;


class WitnessSupportVotes extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];


    public function placeholder()
    {
        return 'Checking transactions...';
    }

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $voteFor = [];
        $voteForHistory = [];
        $forWitness = [];
        $forWitnessHistory = [];
        $allPow = 0;
        $allPowRe = 0;
        $allPowPrx = 0;

        $collection = BchApi::getMongoDbCollection($this->config['account']);
        $data = $collection->find(['op' => 'account_witness_vote'], ['sort' => ['timestamp' => 1]]);
        if ($data) {

            foreach ($data as $datum) {
                $arr['witness'] = $datum['op'][1]['witness'];
                $arr['account'] = $datum['op'][1]['account'];
                $arr['approve'] = $datum['op'][1]['approve'];
                $arr['timestamp'] = $datum['timestamp'];
                $arr['date'] = Date::parse($datum['timestamp'])->format('Y F d H:i:s');
                if ($arr['approve'] == true) {
                    $arr['status'] = 'Approve';
                }
                if ($arr['approve'] == false) {
                    $arr['status'] = 'Disapprove';
                }

                if ($datum['op'][1]['account'] == $this->config['account']) { // account votes for other witnesses

                    $voteFor[$arr['witness']] = $arr;
                    $voteForHistory[] = $arr;
                    if ($datum['op'][1]['approve'] == false) {
                        unset($voteFor[$arr['witness']]);
                    }
                }
                if (($datum['op'][1]['account'] != $this->config['account'])||($datum['op'][1]['account']==$arr['witness'])){ // votes for witness
                    $forWitness[$arr['account']] = $arr;
                    $forWitnessHistory[] = $arr;
                    if ($datum['op'][1]['approve'] == false) {
                        unset($forWitness[$arr['account']]);
                    } else {
                        $accountData = FullCurrentDataOfAccount::get($arr['account']);

                        $power = str_replace(' GESTS', '', $accountData[0]['vesting_shares']);
                        $power = str_replace(' VESTS', '', $power);
                        $power = str_replace(' SHARES', '', $power);
                        $proxy = $accountData[0]['proxied_vsf_votes']['0']/1000000;
                        $forWitness[$arr['account']]['proxy'] = round($proxy + $power);
                    }
                }

            }
        }


        $voteFor = collect($voteFor)->sortByDesc('timestamp');
        $voteForHistory = collect($voteForHistory)->sortByDesc('timestamp');
        $forWitness = collect($forWitness)->sortByDesc('proxy');
        $forWitnessHistory = collect($forWitnessHistory)->sortByDesc('timestamp');
        $allPowPrx = $forWitness->sum('proxy');

        return view(getenv('BCH_API') . '.widgets.witness_support_votes', [
            'config' => $this->config,
            'voteFor' => $voteFor->toArray(),
            'forWitness' => $forWitness->toArray(),
            'account' => $this->config['account'],
            'forWitnessHistory' => $forWitnessHistory->toArray(),
            'voteForHistory' => $voteForHistory->toArray(),
            'allPowPrx' => number_format($allPowPrx/1000000,3,'.',' '),
        ]);
    }
}
