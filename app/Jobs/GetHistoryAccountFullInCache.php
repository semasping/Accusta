<?php

namespace App\Jobs;

use App\semas\AdminNotify;
use App\semas\BchApi;
use App\semas\GolosApi;
use App\semas\SteemitApi;
use App\semas\VizApi;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GetHistoryAccountFullInCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $acc;
    private $api;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($acc, $api = 'golos')
    {
        $this->acc = $acc;
        $this->api = $api;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        dump('Start getting ' . $this->acc, $this->api);
        try {
            if ($this->api == 'golos') //GolosApi::getHistoryAccountFullInCache($this->acc);
            {
                GolosApi::getHistoryAccountFullInDBDesc($this->acc);
            }
            if ($this->api == 'steemit') //SteemitApi::getHistoryAccountFullInCache($this->acc);
            {
                SteemitApi::getHistoryAccountFullInDBDesc($this->acc);
            }
            if ($this->api == 'viz') //SteemitApi::getHistoryAccountFullInCache($this->acc);
            {
                VizApi::getHistoryAccountFullInDBDesc($this->acc);
            }
        } catch (Exception $e) {
            dump($e->getTraceAsString());
            AdminNotify::send('Jobs failed: ' . print_r($e->getTraceAsString(), true));

            if ($this->api == 'golos') {
                GolosApi::disconnect();
            }
            if ($this->api == 'steemit') {
                SteemitApi::disconnect();
            }
            if ($this->api == 'viz') {
                VizApi::disconnect();
            }
        }
        dump('-------done--');

    }
}
