<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 18.08.2017
 * Time: 18:50
 */

namespace App\semas;

ini_set('memory_limit', '2500M');

use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\Commands;
use GrapheneNodeClient\Commands\Single\BroadcastTransactionSynchronousCommand;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Tools\Transaction;
use Illuminate\Support\Facades\Cache;
use WebSocket\Exception;
use MongoDB;

class GolosApi
{
    public static $attempt = 0;

    private static $connector = null;

    public static function getConnector()
    {
        if (self::$connector == null) {
            //self::$connector = new GolosWsConnector();
            self::$connector = new GolosApiWsConnector();
        }

        return self::$connector;
    }

    public static function getHistoryAccount($acc, $from, $limit = 2000)
    {
        $key = "3golos_getacchistory.$acc.$from.$limit";
        if (Cache::get($key . '_status') != 'working') {
            Cache::put($key . '_status', 'working', 2);
            if ($from % $limit == 0) {
                //AdminNotify::send("to set cache getHistoryAccount($acc, $from, $limit) key:$key");
                //if ($acc==' vp-bodyform')
                //Cache::forget("2golos_getacchistory.vp-bodyform.$from");

                $history = Cache::rememberForever($key,
                    function () use ($acc, $from, $limit) {
                        AdminNotify::send("golos to set cache getHistoryAccount($acc, $from, $limit) in function");

                        return self::_getAccHistory($acc, $from, $limit);
                    });
                if (count($history)!=$limit) {
                    AdminNotify::send('count($history)='.count($history));
                    Cache::forget($key);
                    Cache::put($key . '_status', 'fail', 2);
                    return self::getHistoryAccount($acc, $from, $limit);
                    //dump($acc,$history);
                }
                //
                //self::setCurrentCachedTransactionId($acc, $from);
                Cache::put($key . '_status', 'done', 2);
                //dump($key.' done');

                return $history;


            } else {
                //AdminNotify::send("without cache getHistoryAccount($acc, $from, $limit)");
                Cache::put($key . '_status', 'done', 2);
                //dump($key.' done');

                return self::_getAccHistory($acc, $from, $limit);
            }
        } else {
            sleep(5);
            //dump($key.' wait');

            return self::getHistoryAccount($acc, $from, $limit);
        }
    }

    private static function _getAccHistory($acc, $from, $limit)
    {
        $content = '';
        if ($from > 1) {
            if ($from < $limit) {
                AdminNotify::send("from=$from;limit=$limit");
                $limit = $from;
            }
        }
        try {
            //$command = new GetAccountHistoryCommand(self::getConnector());
            $command = new Commands(self::getConnector());
            $command = $command->get_account_history();

            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0', $acc);
            $commandQuery->setParamByKey('1', $from);
            $commandQuery->setParamByKey('2', $limit);

            //AdminNotify::send("_getAccHistory($acc, $from, $limit)");

            $content = $command->execute($commandQuery);
            //dd($content);
        } catch (Exception $e) {
            dump($e);
            //self::disconnect();
            return self::checkResult($content, '_getAccHistory', [$acc, $from, $limit]);
        }

        return self::checkResult($content, '_getAccHistory', [$acc, $from, $limit]);
    }

    public static function getHistoryAccountLast($acc)
    {
        $res = self::_getAccHistory($acc, -1, 0);

        //AdminNotify::send("max = getHistoryAccountLast($acc) = " . print_r($res[0][0], true));
//dump($res);
        return $res[0][0];
    }

    public static function getHistoryAccountFirst($acc)
    {
        $res = self::_getAccHistory($acc, 0, 0);

        //AdminNotify::send("max = getHistoryAccountFirst($acc) = " . print_r($res, true));
//dump($res);
        return $res[0][1];
    }

    public static function getHistoryAccountAll($acc)
    {
        $max = self::getHistoryAccountLast($acc);

        return Cache::rememberForever('golos_resulthistory' . $acc . $max,
            function () use ($max, $acc) {
                $history = [];
                $qq = 0;
                $h = 0;
                $i = 2000;
                $limit = 2000;
                while ($i <= $max) {
                    $his = self::getHistoryAccount($acc, $i, $limit);
                    foreach ($his['result'] as $item) {
                        $history[$h++] = $item;
                    }
                    unset($his);

                    $i = $i + 2000;
                    if ($i > $max) {
                        //AdminNotify::send('i' . $i);
                        $i = $i - 2000;
                        $limit = $max - $i;
                        $i = $max;
                    }
                    if ($limit == 0) {
                        //AdminNotify::send('limit =0 exit');
                        break;
                    }
                    $qq++;
                    if ($qq > 5000) {
                        AdminNotify::send('$qq > 5000 in GolosApi.php:133');
                        break;
                    }
                }

                return $history;
            });
    }

    public static function getHistoryAccountFullInCache($acc)
    {
        $max = self::getHistoryAccountLast($acc);

        return Cache::rememberForever('golos_resulthistory' . $acc . $max,
            function () use ($max, $acc) {
                //$history = [];
                $qq = 0;
                //$h = 0;
                $i = 2000;
                $limit = 2000;
                if ($i > $max) {
                    $i = $max;
                    $limit = $max;
                }
                while ($i <= $max) {
                    if ($his = self::getHistoryAccount($acc, $i, $limit)) {
                        self::setCurrentCachedTransactionId($acc, $i);
                    }


                    $i = $i + 2000;
                    if ($i > $max) {
                        //AdminNotify::send('i' . $i);
                        $i = $i - 2000;
                        $limit = $max - $i;
                        $i = $max;
                    }
                    if ($limit == 0) {
                        //AdminNotify::send('limit =0 exit');
                        break;
                    }
                    $qq++;
                    if ($qq > 5000) {
                        AdminNotify::send('$qq > 5000 in GolosApi.php:133');
                        break;
                    }
                }
                //self::setCurrentCachedTransactionId($acc,$max);
                return true;
            });
    }


    public static function getHistoryAccountFullInDBDesc($acc)
    {
        $max = self::getHistoryAccountLast($acc);
        //$return = false;
        $key = "1glsGetFullAccHisToDB.$acc.$max";
        $key2 = "1glsGetFullAccHisToDBHis.$acc";
        if (Cache::get($key2 . '_status') != 'working' && Cache::get($key2 . '_status') != 'done') {
            dump($key2);
            Cache::put($key2 . '_status', 'working', 1);
            $t = $max;
            $limit = 5000;
            while ($t >= 0) {
                $timestart = microtime(true);
                if ($transactions = self::getHistoryAccount($acc, $t, $limit)) {
                    $time1 = microtime(true);
                    $reTra = [];
                    foreach ($transactions as $transaction) {
                        $reTra[] = self::prepare_transactions($transaction);
                    }
                    //dump($reTra);
                    $time2 = microtime(true);
                    try {
                        $collection = BchApi::getMongoDbCollection($acc);
                        //dump($collection);
                        $collection->insertMany($reTra, ['ordered' => false]);
                        self::setCurrentCachedTransactionId($acc, $t);
                        dump($key, $t, 'finish');
                    } catch (\MongoDuplicateKeyException $e) {
                        dump('already exist');
                    } catch (\MongoException $e) {
                        dump('excepshen', $e->getMessage());
                    } catch (\Exception $e) {
                        dump('excepshen', $e->getMessage());
                    }


                    $time3 = microtime(true);

                    Cache::put($key2 . '_status', 'working', 1);
                    dump($time1 - $timestart, $time2 - $timestart, $time3 - $timestart, $time3 - $timestart);

                }
                //$time4 = microtime(true);

                $t = $t - 5001;
                if ($t < 5000) {
                    $limit = $t;
                }
            }

            Cache::put($key2 . '_status', 'done', 1);
        }
    }

    public static function getHistoryAccountUpdateInDBDesc($acc, $processed)
    {
        $max = self::getHistoryAccountLast($acc);
        //$return = false;
        $key = "1glsGetUpdateAccHisToDB.$acc.$max";
        $key2 = "1glsGetUpdateAccHisToDBHis.$acc";
        if (Cache::get($key2 . '_status') != 'working' && Cache::get($key2 . '_status') != 'done') {
            dump($key2);
            Cache::put($key2 . '_status', 'working', 1);
            $t = $max;
            $limit = 500;
            if ($t - $processed < $limit) {
                $limit = $t - $processed;
            }
            while ($processed <= $t) {
                $timestart = microtime(true);
                if ($transactions = self::getHistoryAccount($acc, $t, $limit)) {
                    $time1 = microtime(true);
                    $reTra = [];
                    foreach ($transactions as $transaction) {
                        $reTra[] = self::prepare_transactions($transaction);;
                    }
                    //dump($reTra);
                    $time2 = microtime(true);
                    try {
                        $collection = BchApi::getMongoDbCollection($acc);
                        //dump($collection);
                        $collection->insertMany($reTra, ['ordered' => false]);
                        self::setCurrentCachedTransactionId($acc, $t);
                        dump($key, $t, 'finish');
                    } catch (\MongoDuplicateKeyException $e) {
                        dump('already exist');
                    } catch (\MongoException $e) {
                        dump('excepshen 1', $e->getMessage());
                    } catch (\Exception $e) {
                        dump('excepshen 2', $e->getMessage());
                    }


                    $time3 = microtime(true);

                    Cache::put($key2 . '_status', 'working', 1);
                    dump($time1 - $timestart, $time2 - $timestart, $time3 - $timestart, $time3 - $timestart);

                }
                //$time4 = microtime(true);

                $t = $t - ($limit+1);
                $processed = $processed + $limit;
                if ($t > 0) {
                    if ($t < $limit) {
                        $limit = $t;
                    }
                }
            }

            Cache::put($key2 . '_status', 'done', 1);
        }
    }

    public static function getHistoryAccountAllWCallback($acc, $fn)
    {
        $max = self::getHistoryAccountLast($acc);

        return Cache::rememberForever('golos_resulthistory' . $acc . $max,
            function () use ($max, $acc, $fn) {
                $history = [];
                $qq = 0;
                $h = 0;
                $i = 2000;
                $limit = 2000;
                while ($i <= $max) {
                    $his = self::getHistoryAccount($acc, $i, $limit);
                    foreach ($his as $item) {
                        $history[$h++] = call_user_func($fn, [$item]);
                    }
                    unset($his);

                    $i = $i + 2000;
                    if ($i > $max) {
                        //AdminNotify::send('i' . $i);
                        $i = $i - 2000;
                        $limit = $max - $i;
                        $i = $max;
                    }
                    if ($limit == 0) {
                        //AdminNotify::send('limit =0 exit');
                        break;
                    }
                    $qq++;
                    if ($qq > 500) {
                        //AdminNotify::send('exit');
                        break;
                    }
                }

                return $history;
            });
    }

    public static function getVotes($acc)
    {
        //$command = new GetAccountVotesCommand(self::getConnector());
        $command = new Commands(self::getConnector());
        $command = $command->get_account_votes();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $acc);
        $content = $command->execute($commandQuery);

        return $content;
    }

    public static function getContent($author, $permlink)
    {
        $content = '';
        try {
//            $command = new GetContentCommand(self::getConnector());
            $command = new Commands(self::getConnector());
            $command = $command->get_content();

            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0', $author);
            $commandQuery->setParamByKey('1', $permlink);
            $content = $command->execute($commandQuery);
        } catch (Exception $e) {
            //self::disconnect();

            return self::checkResult($content, 'getContent', [$author, $permlink]);
        }

        return self::checkResult($content, 'getContent', [$author, $permlink]);

    }

    public static function getDiscussionsByBlog($author, $limit, $start_author = null, $start_permlink = null)
    {
        $content = '';
        try {
            //$command = new GetDiscussionsByBlogCommand(self::getConnector());
            $command = new Commands(self::getConnector());
            $command = $command->get_discussions_by_blog();

            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0:limit', $limit);
            $commandQuery->setParamByKey('0:select_tags', []);
            $commandQuery->setParamByKey('0:select_authors', [$author]);
            $commandQuery->setParamByKey('0:truncate_body', null);
            $commandQuery->setParamByKey('0:start_author', $start_author);
            $commandQuery->setParamByKey('0:start_permlink', $start_permlink);
            $commandQuery->setParamByKey('0:parent_author', null);
            $commandQuery->setParamByKey('0:parent_permlink', null);
            $content = $command->execute($commandQuery);
        } catch (Exception $e) {
            //self::disconnect();

            return self::checkResult($content, 'getDiscussionsByBlog', [$author]);

        }

        return self::checkResult($content, 'getDiscussionsByBlog', [$author]);

    }

    public static function getDiscussionsByAuthorBeforeDate($author, $before_date, $limit, $start_permlink = '')
    {
        $content = '';
        try {
            //$command = new GetContentCommand(self::getConnector());
            $command = new Commands(self::getConnector());
            $command = $command->get_content();

            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0', $author);
            $commandQuery->setParamByKey('1', $start_permlink);
            $commandQuery->setParamByKey('2', $before_date);
            $commandQuery->setParamByKey('3', $limit);
            $content = $command->execute($commandQuery);
        } catch (Exception $e) {
            //self::disconnect();

            return self::checkResult($content, 'getDiscussionsByAuthorBeforeDate',
                [$author, $before_date, $limit, $start_permlink]);

        }

        return self::checkResult($content, 'getDiscussionsByAuthorBeforeDate',
            [$author, $before_date, $limit, $start_permlink]);


    }


    public static function getAccountFull($acc)
    {
        $content = '';
        try {
            //$command = new GetAccountCommand(self::getConnector());
            $command = new Commands(self::getConnector());
            $command = $command->get_accounts();

            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0', [$acc]);
            $content = $command->execute($commandQuery);

        } catch (Exception $e) {
            self::disconnect();

            return self::checkResult($content, 'getAccountFull', [$acc]);

        }

        return self::checkResult($content, 'getAccountFull', [$acc]);
    }

    public static function getAccountsCount()
    {
        //$command = new GetAccountCountCommand(self::getConnector());
        $command = new Commands(self::getConnector());
        $command = $command->get_account_count();

        $commandQuery = new CommandQueryData();
        $content = $command->execute($commandQuery);

        return $content;
    }

    public static function getCurrentPrice()
    {
        //$command = new GetCurrentMedianHistoryPriceCommand(self::getConnector());
        $command = new Commands(self::getConnector());
        $command = $command->get_current_median_history_price();

        $commandQuery = new CommandQueryData();
        $content = $command->execute($commandQuery);

        return $content;
    }

    public static function getBlockHeader($block)
    {
        //$command = new GetBlockHeaderCommand(self::getConnector());
        $command = new Commands(self::getConnector());
        $command = $command->get_block_header();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $block);
        $content = $command->execute($commandQuery);

        return $content;
    }

    public static function disconnect()
    {
        $connect = self::getConnector();

        $connect->destroyConnection();
        AdminNotify::send('GolosDisconnect');
    }

    public static function GetDynamicGlobalProperties()
    {
        $content = '';
        try {
            //$command = new GetDynamicGlobalPropertiesCommand(self::getConnector());
            $command = new Commands(self::getConnector());
            $command = $command->get_dynamic_global_properties();

            $commandQuery = new CommandQueryData();
            $content = $command->execute($commandQuery);
        } catch (Exception $e) {
            //self::disconnect();
            //dd($content);
            return self::checkResult($content, 'GetDynamicGlobalProperties');
        }

        return self::checkResult($content, 'GetDynamicGlobalProperties');
    }

    public static function getBlock($block_id)
    {

        $content = '';
        try {
            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0', $block_id);

            //$command = new GetBlockCommand(new GolosWSConnector());
            $command = new Commands(self::getConnector());
            $command = $command->get_block();

            $content = $command->execute($commandQuery);
        } catch (Exception $e) {
            self::disconnect();

            return self::checkResult($content, 'getBlock', [$block_id]);
        }

        return self::checkResult($content, 'getBlock', [$block_id]);
    }

    private static function checkResult($content, $f, $params = [])
    {
        if (isset($content['result'])) {
            return $content['result'];
        } else {
            self::disconnect();
            if (self::$attempt < 3) {
                self::$attempt++;
                AdminNotify::send('Golos reconnect function:' . $f . ' attempt:' . self::$attempt . ' Error:' . print_r($content,
                        true));
                return call_user_func_array(array('self', $f), $params);
                //return self::$f();
            }

            return false;
        }
    }

    static function getPrice()
    {
        return Cache::remember('golos_getPrice_', 10, function () {
            $resp = self::GetDynamicGlobalProperties();
            AdminNotify::send(print_r($resp, true));
            if (is_array($resp)) {
                $q1 = str_replace(' GOLOS', '', $resp['total_vesting_fund_steem']);
                $q2 = str_replace(' GESTS', '', $resp['total_vesting_shares']);
                //AdminNotify::send($q1 .'/'. $q2);
                $p = $q1 / $q2;
                AdminNotify::send($p * 1000000);

                return round($p * 1000000, 3, PHP_ROUND_HALF_DOWN);
            }

            return false;
        });

    }

    static function convertToSg($gests)
    {
        $SG = $gests / 1000000 * self::getPrice();
        $SG = round($SG, 3, PHP_ROUND_HALF_DOWN);
        return $SG;
    }

    /**
     * @param $acc
     * @param $type : [
     * "vote" => "",
     * "transfer" => "",
     * "comment" => "",
     * "transfer_to_vesting" => "",
     * "curation_reward" => "",
     * "author_reward" => "",
     * "account_update" => "",
     * "account_create" => "",
     * "interest" => "",
     * "custom_json" => "",
     * "delete_comment" => "",
     * "transfer_to_savings" => "",
     * "convert" => "",
     * "account_witness_vote" => "",
     * "fill_convert_request" => "",
     * "comment_options" => "",
     * "withdraw_vesting" => "",
     * "fill_vesting_withdraw" => ""
     * ];
     * @return mixed
     */
    public static function getTransaction($acc, $type)
    {
        $history = Cache::remember('6his' . $acc . $type, 10, function () use ($acc, $type) {
            $max = self::getHistoryAccountLast($acc);
            $history = [];
            //$data = [];
            $qq = 0;
            $h = 0;
            $i = 2000;
            $limit = 2000;
            if ($i > $max) {
                $i = $max;
                $limit = $max;
            }
            while ($i <= $max) {
                $cache_key = '6his' . $acc . $type . $i . $limit;
                //AdminNotify::send($cache_key);
                $history_n = Cache::rememberForever($cache_key,
                    function () use ($acc, $i, $limit, $type, $h, $cache_key) {
                        $his = self::getHistoryAccount($acc, $i, $limit);
                        //dump(current($his));
                        $history = [];
                        foreach ($his as $item) {
                            if (isset($item[1]['op'])) {
                                //dump($item);
                                $type_op = $item[1]['op'][0];
                                if ($type == $type_op) {
                                    $container = $item[1]['op'][1];
                                    $container['trx_id'] = $item[1]['trx_id'];
                                    $container['block'] = $item[1]['block'];
                                    $container['timestamp'] = $item[1]['timestamp'];
                                    $container['op'] = $item[1]['op'][0];
                                    $history[$h] = $container;
                                }
                            }
                            $h++;
                        }
                        ////AdminNotify::send('in '.$cache_key.":_".count($history));
//dump(($history));
                        return $history;
                    });
                $history = array_merge($history, $history_n);
                unset($his);
                $i = $i + 2000;
                if ($i > $max) {
                    //AdminNotify::send('i' . $i);
                    $i = $i - 2000;
                    $limit = $max - $i;
                    $i = $max;
                }
                if ($limit == 0) {
                    //AdminNotify::send('limit =0 exit');
                    break;
                }
                $qq++;
                if ($qq > 10000) {
                    AdminNotify::send('$qq > 10000 exit');
                    break;
                }

            }
            //AdminNotify::send(count($history));

            return $history;
        });
        return $history;
    }

    public static function getData($acc, $max)
    {
        AdminNotify::send('tr' . $acc . $max);
        return Cache::remember('tr' . $acc . $max, 10, function () use ($max, $acc) {
            $history = [];
            $data = [];
            $account_create = [];
            $author_data = [];
            $kur_data = [];
            $post_data = [];
            $post_temp = [];
            $transfer_out_data = [];
            $qq = 0;
            $h = 0;
            $i = 2000;
            $limit = 2000;
            if ($i > $max) {
                $i = $max;
                $limit = $max;
            }
            while ($i <= $max) {

                $his = self::getHistoryAccount($acc, $i, $limit);
                //dump($acc,$his);

                foreach ($his as $item) {
                    if (isset($item[1]['op'])) {
                        //dump($item);
                        $type_op = $item[1]['op'][0];


                        if (isset($item[1]['op']) && $item[1]['op'][0] == 'account_create') {
                            //AdminNotify::send(print_r($his, true));
                            //dump($item);
                            //    $reward = $this->processReward($item);
                            //$block = $item[1]['block'];
                            $account_create['block'] = $item[1]['block'];
                            $account_create['timestamp'] = $item[1]['timestamp'];
                            //->delay($date);
                        }


                        if (isset($item[1]['op']) && $item[1]['op'][0] == 'author_reward') {
                            //AdminNotify::send(print_r($his, true));
                            //dump($item);
                            //    $reward = $this->processReward($item);

                            //$block = $item[1]['block'];
                            $permlink = $item[1]['op'][1]['permlink'];
                            $author_data[$permlink]['block'] = $item[1]['block'];
                            $author_data[$permlink]['timestamp'] = $item[1]['timestamp'];
                            $author_data[$permlink]['permlink'] = $item[1]['op'][1]['permlink'];
                            $author_data[$permlink]['GBG'] = str_replace(' GBG', '', $item[1]['op'][1]['sbd_payout']);
                            $author_data[$permlink]['GOLOS'] = str_replace(' GOLOS', '', $item[1]['op'][1]['steem_payout']);
                            $author_data[$permlink]['GESTS'] = str_replace(' GESTS', '', $item[1]['op'][1]['vesting_payout']);

                            //->delay($date);
                        }

                        if (isset($item[1]['op']) && $item[1]['op'][0] == 'curation_reward') {
                            //AdminNotify::send(print_r($his, true));
                            //dump($item);
                            //    $reward = $this->processReward($item);
                            //$block = $item[1]['block'];

                            $permlink = $item[1]['op'][1]['comment_permlink'];
                            $kur_data[$permlink]['block'] = $item[1]['block'];
                            $kur_data[$permlink]['timestamp'] = $item[1]['timestamp'];
                            $kur_data[$permlink]['author'] = $item[1]['op'][1]['comment_author'];
                            $kur_data[$permlink]['permlink'] = $item[1]['op'][1]['comment_permlink'];
                            $kur_data[$permlink]['GESTS'] = str_replace(' GESTS', '', $item[1]['op'][1]['reward']);
                            //->delay($date);
                        }

                        if (isset($item[1]['op']) && $item[1]['op'][0] == 'comment' && $item[1]['op'][1]['parent_author'] == '') {
                            //AdminNotify::send(print_r($his, true));
                            //dump($item);
                            //    $reward = $this->processReward($item);
                            $permlink = $item[1]['op'][1]['permlink'];
                            if (!in_array($permlink, $post_temp)) {
                                //$block = $item[1]['block'];
                                $post_data[$permlink]['block'] = $item[1]['block'];
                                $post_data[$permlink]['timestamp'] = $item[1]['timestamp'];
                                $post_data[$permlink]['permlink'] = $permlink;
                                $post_temp[$permlink] = $permlink;
                            }
                            //->delay($date);
                        }

                        if (isset($item[1]['op']) && $item[1]['op'][0] == 'transfer' && $item[1]['op'][1]['from'] == $acc) {
                            //AdminNotify::send(print_r($his, true));
                            //dump($item);
                            //    $reward = $this->processReward($item);
                            //$block = $item[1]['block'];
                            //$permlink = $item[1]['op'][1]['permlink'];
                            $transfer_out_data[$h]['block'] = $item[1]['block'];
                            $transfer_out_data[$h]['timestamp'] = $item[1]['timestamp'];
                            //$post_data[$h]['permlink'] = $item[1]['op'][1]['permlink'];
                            $transfer_out_data[$h]['to'] = $item[1]['op'][1]['to'];
                            $transfer_out_data[$h]['amount'] = $item[1]['op'][1]['amount'];
                            $transfer_out_data[$h]['memo'] = $item[1]['op'][1]['memo'];
                            //->delay($date);
                        }
                        $history[$type_op][] = $item;

                    } else {
                        //echo 1;
                        dump($item);
                        //AdminNotify::send(print_r($item,true));
                    }
                    $h++;
                }
                unset($his);
                $i = $i + 2000;
                if ($i > $max) {
                    //AdminNotify::send('i' . $i);
                    $i = $i - 2000;
                    $limit = $max - $i;
                    $i = $max;
                }
                if ($limit == 0) {
                    //AdminNotify::send('limit =0 exit');
                    break;
                }
                $qq++;
                if ($qq > 100000) {
                    AdminNotify::send('$qq > 100000 exit');
                    break;
                }


            }
            $data['account_create'] = $account_create;
            $data['author_reward'] = $author_data;
            $data['curation_reward'] = $kur_data;
            $data['posts'] = $post_data;
            $data['transfer_out_data'] = $transfer_out_data;

            //dump($post_data);
            /*for ($i = 0; $i < 100; $i++) {

                //dump($history['transfer'][$i]);
            }*/
            AdminNotify::send('i' . $i);
            //mail('semasping@gmail.com','laravel vp',print_r($post_data,true));
            return $data;
        });
    }

    public
    static function getFollowers(
        $account
    ) {
        //$command = new GetFollowersCommand(self::getConnector());
        $command = new Commands(self::getConnector());
        $command = $command->get_followers();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $account);
        $commandQuery->setParamByKey('1', '');
        $commandQuery->setParamByKey('2', 'blog');
        $commandQuery->setParamByKey('3', 1000);
        $content = $command->execute($commandQuery);

        return $content;
    }

    public
    static function getPostsByAuthor(
        $author
    ) {

    }

    public
    static function getPostsAll(
        $author
    ) {
        $post_more = true;
        $posts = [];
        while ($post_more) {
            $res_posts = self::getDiscussionsByBlog($author, $limit = 100);
            $posts = array_merge($posts, $res_posts);
            if (count($res_posts) < $limit) {
                $post_more = false;
            }
        }
        return $posts;
    }

    public
    static function getCurrentProcessedHistoryTranzId(
        $acc
    ) {
        $current = 0;
        $key = self::getKeyCurrentCachedTransaction($acc);
        if (Cache::has($key)) {
            $current = Cache::get($key);
        }
        return $current;
    }

    public
    static function getKeyCurrentCachedTransaction(
        $acc
    ) {
        return '1current_cache_transactions_' . $acc;
    }

    private
    static function setCurrentCachedTransactionId(
        $acc,
        $from
    ) {
        $key = self::getKeyCurrentCachedTransaction($acc);
        Cache::forever($key, $from);
        //dump($key, $from, 'finish');
    }

    /**
     * @param $transaction
     * @return mixed
     */
    private static function prepare_transactions($transaction)
    {
        $trns = $transaction['1'];
        $trns['_id'] = (integer)$transaction[0];
        $trns['type'] = $trns['op'][0];

        $trns['date'] = (new MongoDB\BSON\UTCDateTime(strtotime($trns['timestamp']) * 1000));

        if ($trns['op'][0] == 'producer_reward') {
            $trns['op'][1]['VESTS'] = (double)((str_replace(' VESTS', '',
                $trns['op'][1]['vesting_shares'])));
        }
        if ($trns['op'][0] == 'claim_reward_balance') {
            $trns['op'][1]['STEEM'] = (double)((str_replace(' STEEM', '',
                $trns['op'][1]['reward_steem'])));
            $trns['op'][1]['SBD'] = (double)((str_replace(' SBD', '', $trns['op'][1]['reward_sbd'])));
            $trns['op'][1]['VESTS'] = (double)((str_replace(' VESTS', '',
                $trns['op'][1]['reward_vests'])));
        }
        if ($trns['op'][0] == 'author_reward') {
            $trns['op'][1]['STEEM'] = (double)((str_replace(' STEEM', '',
                $trns['op'][1]['steem_payout'])));
            $trns['op'][1]['SBD'] = (double)((str_replace(' SBD', '', $trns['op'][1]['sbd_payout'])));
            $trns['op'][1]['VESTS'] = (double)((str_replace(' VESTS', '',
                $trns['op'][1]['vesting_payout'])));
        }
        if ($trns['op'][0] == 'comment_benefactor_reward') {
            $trns['op'][1]['VESTS'] = (double)((str_replace(' VESTS', '', $trns['op'][1]['reward'])));
        }
        if ($trns['op'][0] == 'curation_reward') {
            $trns['op'][1]['VESTS'] = (double)((str_replace(' VESTS', '', $trns['op'][1]['reward'])));
        }
        return $trns;
        /*if ($trns['op'][0] == 'transfer') {
            $trns['op'][1]['STEEM'] = (double)((str_replace(' STEEM', '', $trns['op'][1]['amount'])));
            $trns['op'][1]['SBD'] = (double)((str_replace(' SBD', '', $trns['op'][1]['amount'])));
            $trns['op'][1]['VESTS'] = (double)((str_replace(' VESTS', '', $trns['op'][1]['reward_vests'])));
        }*/
    }

    /**
     * Receives all operations in the block
     * @param $block_id
     * @return array
     */
    public static function getOpsInBlock($block_id)
    {
        $content = '';
        try {
            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0', $block_id);
            $commandQuery->setParamByKey('1', false);

            $command = new Commands(self::getConnector());
            $command = $command->get_ops_in_block();

            $content = $command->execute($commandQuery);
            //dump($content);
        } catch (Exception $e) {
            GolosApi::disconnect();

            return self::checkResult($content, 'getOpsInBlock', [$block_id]);
        }

        return self::checkResult($content, 'getOpsInBlock', [$block_id]);
    }

    /**
     * Package sending GOLOS/GBG to any accounts
     * @param array $accounts //example [['to'=>'semasping','amount'=>'0.001 GOLOS','memo'=>'test'],['to'=>'semasping','amount'=>'0.002 GBG','memo'=>'test2']]
     * @param $from
     * @param $wifFrom // active key
     * @return bool
     */
    public static function sendTokenToMany($accounts, $from, $wifFrom)
    {
        $command = new BroadcastTransactionSynchronousCommand(self::getConnector());
        $chainName = self::getConnector()->getPlatform();
        $chunks = array_chunk($accounts, 50);
        foreach ($chunks as $chunk) {
            $tx = Transaction::init(self::getConnector());
            $opNumber = 0;
            foreach ($chunk as $data) {
                $tx->setParamByKey(
                    '0:operations:' . ($opNumber++),
                    [
                        'transfer',
                        [
                            'from' => $from,
                            'to' => $data['to'],
                            'amount' => $data['amount'],
                            'memo' => $data['memo']
                        ]
                    ]
                );

            }
            Transaction::sign($chainName, $tx, ['active' => $wifFrom]);
            $answer = $command->execute(
                $tx
            );
            if (isset($answer['result']['block_num']) && $answer['result']['block_num'] > 0) {
                $m = PHP_EOL . date('Y.m.d H:i:s') . " - accounts got tokens in block {$answer['result']['block_num']}";
                $return = $answer['result']['block_num'];
            } else {
                $m = PHP_EOL . date('Y.m.d H:i:s') . ' - error during sending tokens ';
                $return = false;
            }
            echo $m;
            AdminNotify::send($m);
            return $return;
        }

    }

    public static function vote($acc, $key, $permlink, $author)
    {
        $command = "node GolosVote.js --acc=$acc --pp=$permlink --pa=$author";
        exec($command . " --key=$key", $o, $r);
        dump($o, $r);
        $alreadyVote = collect($o)->filter(function ($item) {
            if (strpos($item, 'You have already voted in a similar way') !== false) {
                return $item;
            }
        });
        if ($alreadyVote->count() > 0) {
            AdminNotify::send('Already voted:' . print_r($alreadyVote, true));

            return true;
        }
        $errorVote = collect($o)->filter(function ($item) {
            if (strpos($item, 'Error') !== false) {
                return $item;
            }
        });
        if ($errorVote->count() > 0) {
            AdminNotify::send('Error vote:' . print_r($errorVote, true) . "\n" . $command);

            return false;
        }

        return true;
    }

    public static function comment($acc, $key, $permlink, $author, $body)
    {
        //AdminNotify::send("node GolosComment.js --acc=$acc --key=.... --pp=$permlink --pa=$author --body='$body'");
        //$author = str_replace('.','',$author);
        $command = "node GolosComment.js --acc=$acc --pp=$permlink --pa=$author --body='$body'";
        exec($command . " --key=$key", $o, $r);
        dump($o, $r);
        $errorComment = collect($o)->filter(function ($item) {
            if (strpos($item, 'Error') !== false) {
                return $item;
            }
        });
        if ($errorComment->count() > 0) {
            AdminNotify::send('Error comment:' . print_r($errorComment, true) . "\n" . $command . "\n" . 'body:' . $body);

            return false;
        }

        return true;
    }

    public static function checkFollow($account, $author)
    {
        $followers = self::getFollowers($account);
        //dump($followers);
        $followers = collect($followers)->pluck('follower')->toArray();
        //dump($followers);
        if (in_array($author, $followers)) {
            return true;
        }

        return false;
    }

    public static function getComments($author, $permlink)
    {
        $content = '';
        try {
            //$command = new GetContentRepliesCommand(new GolosWsConnector());

            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0', $author);
            $commandQuery->setParamByKey('1', $permlink);

            $command = new Commands(self::getConnector());

            $command = $command->get_content_replies();
            $content = $command->execute($commandQuery);

        } catch (Exception $e) {
            GolosApi::disconnect();

            return self::checkResult($content, 'getComments', [$author, $permlink]);
        }

        return self::checkResult($content, 'getComments', [$author, $permlink]);
    }

    public static function getPostVotes($author, $permlink)
    {
        $content = '';
        try {

            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0', $author);
            $commandQuery->setParamByKey('1', $permlink);

            $command = new Commands(self::getConnector());

            $command = $command->get_active_votes();
            $content = $command->execute($commandQuery);

        } catch (Exception $e) {
            self::disconnect();

            return self::checkResult($content, 'getPostVotes', [$author, $permlink]);
        }

        return self::checkResult($content, 'getPostVotes', [$author, $permlink]);
    }


    /**
     * @param $post
     * @param $acc
     *
     * @return \Illuminate\Support\Collection
     */
    public static function check_comment($post, $acc)
    {
        $link = $post['link'];
        $result = explode('/@', $link);
        $permlink = explode("/", $result[1]);
        $permlink = $permlink[1];
        $comments = self::getComments($post['author'], $permlink);
        $comments = collect($comments);
        $comment = $comments->where('author', $acc);

        return $comment;
    }
}