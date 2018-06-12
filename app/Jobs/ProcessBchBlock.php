<?php

namespace App\Jobs;

use App\BchBlock;
use App\semas\BchApi;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessBchBlock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $block_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($block_id)
    {
        $this->block_id = $block_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if ($this->block_id == 0 || empty($this->block_id)) {
                throw new \Exception('Block id =0 or empty');
            }
            echo $this->block_id . "\n";

            if (BchBlock::where('id', '=', $this->block_id)->count() <= 0) { // check if not already processed this block

                $data = BchApi::getOpsInBlock($this->block_id);
                if ($data === false) {
                    dump($data);
                    throw new Exception('No data form Blockchain: ' . getenv('BCH_API') . ' for block:' . $this->block_id . ' in job ProcessBchBlock');
                }
                if (!empty($data)) {
                    $full_block = $data;
                    foreach ($full_block as $transaction) {
                        $this->checkAuthorsRewards($transaction);
                        $this->checkPosts($transaction);

                        //$oper[] = $operation;

                    }
                }
                //save processed block to db

                $gb = new BchBlock();
                $gb->id = $this->block_id;
                $gb->status = 'done';
                $gb->save();

                Echo 'Done with block^' . $this->block_id . "\n";
            }else{
                echo 'already processed'."\n";
            }

        } catch (Exception $e) {
            echo "Exception on " . " message: " . $e->getMessage() . '|' . $e->getLine() . '|' . $e->getCode() . "\n";

            // try to get this block later
            $this->release(5);

            exit;
        }

        //file_get_contents('https://hchk.io/952518ea-65f5-410d-979e-58a6d6b016f2'); //@stodo change number

    }

    private function checkAuthorsRewards($operation)
    {
        $action = $operation['op'][0];
        $data = $operation['op'][1];
        if ($action == 'author_reward') {
            //check for post and not diff post
            if (true) {
                $data['timestamp'] = $operation['timestamp'];

                //$this->dispatch((new BchProcessAuthorRewards($operation))->onQueue(getenv('BCH_API') . '_process_author_rewards'));
                BchProcessAuthorRewards::dispatch($operation)->onQueue(getenv('BCH_API') . '_process_author_rewards');

                echo 'sent to BchProcessAuthorRewards' . "\n";
            }
        }
    }

    private function checkPosts($operation)
    {
        $action = $operation['op'][0];
        $data = $operation['op'][1];
        if ($action == 'comment') {
            if ($data['parent_author'] == '' && !str_contains($data['body'],
                    '@@ -')
            ) {
                $data['timestamp'] = $operation['timestamp'];
                //dispatch((new BchProcessNewPostCreated($data))->onQueue('GolosProcess'));
                //dispatch((new GolosProcessNewPost($data))->onQueue('GolosProcessNewPosts'));

                echo 'sent to BchProcessNewPostCreated' . "\n";
            }
        }
    }
}
