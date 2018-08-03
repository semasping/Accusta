<?php

namespace App\Console\Commands;

use App\BchBlock;
use App\Jobs\ProcessBchBlock;
use App\semas\BchApi;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Queue\Jobs\Job;

/**
 * Class BchGetBlocks
 * @package App\Console\Commands
 */
class BchGetBlocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bch:get-block {last_block_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            $last_block_id = $this->getLastProcessedBlock();
            echo 'last_block_id: ' . $last_block_id . "\n";
            $new_block_id = 0;
            $data = BchApi::GetDynamicGlobalProperties();
            if (isset($data['last_irreversible_block_num'])) {
                $block_id = $data['last_irreversible_block_num'];
            } else {
                throw new \Exception('No data from Blockchain');
            }

            $j = $block_id - $last_block_id; //сколько блоков пропустили
            if ($j > 1) {
                while ($last_block_id < $block_id) {
                    $last_block_id++;
                    ProcessBchBlock::dispatch($last_block_id)->onQueue(getenv('BCH_API') . '_default_accusta');
                    file_put_contents('last_block_id_golos', $last_block_id);
                    $this->info('Добавляем пропущенные блоки в обработку Блок: ' . $last_block_id);
                }
            } else {
                $this->info('There is no new blocks');
                $this->info('Last irreversible_bloc^ ' . $block_id);

            }
        } catch (Exception $e) {
            echo "Exception on " . " message: " . $e->getMessage() . '|' . $e->getLine() . '|' . $e->getCode();
            sleep(10);
            //Artisan::call('golos:getblock');
            exit;
        }
    }

    /**
     * @return integer
     */
    private function getLastProcessedBlock()
    {
        $last_block_id = $this->argument('last_block_id');
        if ($last_block_id == 0) {
            $last_block_id = file_get_contents('last_block_id_golos', 0);
            //$last_block_id = BchBlock::latest()->first()->toArray()['id'];
        }
        if ($last_block_id == '') {
            //$last_block_id = file_get_contents('last_block_id_golos',0);
            $last_block_id = BchBlock::latest()->first()->toArray()['id'];
        }
        return $last_block_id;
    }
}
