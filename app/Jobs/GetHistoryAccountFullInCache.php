<?php

namespace App\Jobs;

use App\semas\GolosApi;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GetHistoryAccountFullInCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $acc;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($acc)
    {
        $this->acc = $acc;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        dump('Start getting '. $this->acc);
        GolosApi::getHistoryAccountFullInCache($this->acc);
        dump('-------done--');
    }
}
