<?php

namespace App\Widgets;

use App\semas\BchApi;
use Arrilot\Widgets\AbstractWidget;

class WitnessRewards extends AbstractWidget
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
    public function run($acc)
    {
        //
        $data = collect(BchApi::getTransaction($acc, 'producer_reward'));
        //dump($data);
        $data = $data->map(function ($item, $key) {
            $data = $item;
            if (getenv('BCH_API') == 'golos') {
                $data['GESTS'] = str_replace(' GESTS', '', $item['vesting_shares']);
            }
            if (getenv('BCH_API') == 'steemit') {
                $data['VESTS'] = str_replace(' VESTS', '', $item['vesting_shares']);
            }
            return $data;
        });

        return view(getenv('BCH_API') . '.widgets.witness_rewards', [
            'config' => $this->config,
            'account' => $acc,
            'data' => $data,
        ]);

    }

    public function placeholder()
    {
        return 'Check witness rewards...';
    }
}
