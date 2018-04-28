<?php

namespace App\Widgets;

use App\semas\BchApi;
use Arrilot\Widgets\AbstractWidget;
use Jenssegers\Date\Date;

class AuthorsRewards extends AbstractWidget
{
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
    public function run()
    {
        //

        $collection = BchApi::getMongoDbCollection($this->config['account']);
        //$data = $collection->find(['op'=>'producer_reward']);
        $sums_by_monthes = $collection->aggregate([
            //['$group'=>['_id'=>['date'=>['month'=>['$month'=>'timestamp']]], 'total'=>['$sum'=>'block']]]
            ['$match' => ['type' => ['$eq' => 'curation_reward']]],
            ['$unwind' => '$op'],
            ['$group' => ['_id' => ['date' => ['M' => ['$month' => '$date'], 'Y' => ['$year' => '$date'],]], 'total' => ['$sum' => '$op.VESTS']]],
            //['$sort'=>['$date']]
        ]);
        $sums_all = $collection->aggregate([
            //['$group'=>['_id'=>['date'=>['month'=>['$month'=>'timestamp']]], 'total'=>['$sum'=>'block']]]
            ['$match' => ['type' => ['$eq' => 'curation_reward']]],
            ['$unwind' => '$op'],
            ['$group' => ['_id' => null, 'total' => ['$sum' => '$op.VESTS']]],
        ]);
        //dump(iterator_to_array($sums));
        $monthes = [];
        foreach ($sums_by_monthes as $state) {
            //dump($state['total'],$state['_id']['date']['M'],$state['_id']['date']['Y']);
            //mp($state);
            $date = Date::parse('01.' . $state['_id']['date']['M'] . '.' . $state['_id']['date']['Y']);
            $r['date'] = $date->format('Y F');
            $r['value'] = $state['total'];
            $monthes[$date->format('Ym')] = $r;
        }
        dump($monthes);
        krsort($monthes);

        foreach ($sums_all as $state) {
            $summs['all'] = $state['total'];
        }

        return view('widgets.authors_rewards', [
            'config' => $this->config,
        ]);
    }
}
