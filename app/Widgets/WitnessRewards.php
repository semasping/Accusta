<?php

namespace App\Widgets;

use App\AccountTransaction;
use App\semas\BchApi;
use Arrilot\Widgets\AbstractWidget;
use Jenssegers\Date\Date;
use MongoDB;

class WitnessRewards extends AbstractWidget
{
    public $cacheTime = 1;
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run($acc, $date = false)
    {
        $summs['all'] = 0;

        $data = $this->getRewardsIn($this->config['account']);
        $sums_by_monthes = $data['sums_by_monthes'];
        $summs = $data['allSP']['all'];

        //dump(iterator_to_array($sums));



        //dd(1);
        //$data = collect($data->toArray());
        //dump($data,$acc,$date, $this->config   );
        /*$data = $data->map(function ($item, $key) {
            //$data = $item;
            //dump($data);
            $data['timestamp']=$item['timestamp'];
            if (getenv('BCH_API') == 'golos') {
                $data['GESTS'] = str_replace(' GESTS', '', $item['op.0.vesting_shares']);
            }
            if (getenv('BCH_API') == 'steemit') {
                $data['VESTS'] = str_replace(' VESTS', '', $item['op'][0]['vesting_shares']);
            }
            return $data;
        });*/
        //dump($data);
        /* $data = collect(BchApi::getTransaction($acc, 'producer_reward'));




        if ($date == false) {
            $date = Date::now()->subMonths(2)->startOfMonth();
        }

        $data = $data->where('timestamp', '>=', Date::parse($date)->toAtomString());
*/

        //$summs['all'] = 0;
        return view(getenv('BCH_API') . '.widgets.witness_rewards', [
            'config' => $this->config,
            'account' => $acc,
            'summs' => $summs,
            'summs_by_monthes' => $sums_by_monthes
        ]);

    }

    public function placeholder()
    {
        return 'Check witness rewards...';
    }

    public function getRewardsIn($acc)
    {

        $collection = BchApi::getMongoDbCollection($acc);
        //$data = $collection->find(['op'=>'producer_reward']);
        $sums_by_monthes = $collection->aggregate([
            //['$group'=>['_id'=>['date'=>['month'=>['$month'=>'timestamp']]], 'total'=>['$sum'=>'block']]]
            ['$match' => ['type' => ['$eq' => 'producer_reward']]],
            ['$unwind' => '$op'],
            [
                '$group' => [
                    '_id' => ['date' => ['M' => ['$month' => '$date'], 'Y' => ['$year' => '$date'],]],
                    'total' => ['$sum' => '$op.VESTS']
                ]
            ],
            //['$sort'=>['$date']]
        ]);
        $monthes = [];
        foreach ($sums_by_monthes as $state) {
            //dump($state['total'],$state['_id']['date']['M'],$state['_id']['date']['Y']);
            //mp($state);
            $date = Date::parse('01.' . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);
            $r['date'] = $date->format('Y F');
            $r['value'] = $state['total'];
            $monthes[$date->format('Ym')] = $r;
        }
        //dump($monthes);
        krsort($monthes);

        $summs['all'] = 0;
        $sums_all = $collection->aggregate([
            //['$group'=>['_id'=>['date'=>['month'=>['$month'=>'timestamp']]], 'total'=>['$sum'=>'block']]]
            ['$match' => ['type' => ['$eq' => 'producer_reward']]],
            ['$unwind' => '$op'],
            ['$group' => ['_id' => null, 'total' => ['$sum' => '$op.VESTS']]],
        ]);
        foreach ($sums_all as $state) {
            $summs['all'] = $state['total'];
        }
        return ['allSP' => $summs, 'sums_by_monthes' => $monthes];
    }
}
