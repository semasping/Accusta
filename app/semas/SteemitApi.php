<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 18.08.2017
 * Time: 18:50
 */

namespace App\semas;


use Exception;
use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\DataBase\GetAccountCountCommand;
use GrapheneNodeClient\Commands\DataBase\GetAccountHistoryCommand;
use GrapheneNodeClient\Commands\DataBase\GetAccountVotesCommand;
use GrapheneNodeClient\Commands\DataBase\GetBlockCommand;
use GrapheneNodeClient\Commands\DataBase\GetBlockHeaderCommand;
use GrapheneNodeClient\Commands\DataBase\GetContentCommand;
use GrapheneNodeClient\Commands\DataBase\GetDynamicGlobalPropertiesCommand;
use GrapheneNodeClient\Commands\Follow\GetFollowersCommand;
use GrapheneNodeClient\Connectors\Http\SteemitHttpConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;
use Illuminate\Support\Facades\Cache;

class SteemitApi
{
    public static $attempt = 0;

    public static function getHistoryAccount($acc, $from, $limit = 2000)
    {
        if ($from % 2000 == 0) {
            AdminNotify::send("steemit: to set cache getHistoryAccount($acc, $from, $limit)");

            return Cache::rememberForever("steemit_getacchistory.$acc.$from", function () use ($acc, $from, $limit) {
                AdminNotify::send("to set cache getHistoryAccount($acc, $from, $limit) in function");

                return self::_getAccHistory($acc, $from, $limit);
            });
        } else {
            AdminNotify::send("steemit: without cache getHistoryAccount($acc, $from, $limit)");

            return self::_getAccHistory($acc, $from, $limit);
        }

    }

    private static function _getAccHistory($acc, $from, $limit)
    {
        $command = new GetAccountHistoryCommand(new SteemitWSConnector());

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $acc);
        $commandQuery->setParamByKey('1', $from);
        $commandQuery->setParamByKey('2', $limit);

        AdminNotify::send("steemit: _getAccHistory($acc, $from, $limit)");

        return $content = $command->execute($commandQuery);
    }

    public static function getHistoryAccountLast($acc)
    {
        $res = self::_getAccHistory($acc, -1, 0);
        AdminNotify::send("steemit: max = getHistoryAccountLast($acc) = " . $res['result'][0][0]);

        return $res['result'][0][0];
    }

    public static function getHistoryAccountAll($acc)
    {
        $max = self::getHistoryAccountLast($acc);

        return Cache::rememberForever('steemit_resulthistory' . $acc . $max, function () use ($max, $acc) {
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
                    AdminNotify::send('i' . $i);
                    $i = $i - 2000;
                    $limit = $max - $i;
                    $i = $max;
                }
                if ($limit == 0) {
                    AdminNotify::send('limit =0 exit');
                    break;
                }
                $qq++;
                if ($qq > 500) {
                    AdminNotify::send('exit');
                    break;
                }
            }

            return $history;
        });
    }

    public static function getVotes($acc)
    {
        $command = new GetAccountVotesCommand(new SteemitWSConnector());

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $acc);
        $content = $command->execute($commandQuery);

        return $content;
    }

    public static function getContent($author, $permlink)
    {
        $content = '';
        try {
            $command = new GetContentCommand(new SteemitHttpConnector());

            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0', $author);
            $commandQuery->setParamByKey('1', $permlink);
            $content = $command->execute($commandQuery);

        } catch (Exception $e) {
            self::disconnect();

            return self::checkResult($content, 'getContent', [$author, $permlink]);
        }

        return self::checkResult($content, 'getContent', [$author, $permlink]);
    }

    public static function getAccountsCount()
    {
        $command = new GetAccountCountCommand(new SteemitHttpConnector());

        $commandQuery = new CommandQueryData();
        $content = $command->execute($commandQuery);

        return $content;
    }

    public static function getBlockHeader($block)
    {
        $command = new GetBlockHeaderCommand(new SteemitHttpConnector());

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $block);
        $content = $command->execute($commandQuery);

        return $content;
    }

    public static function getBlockHeader2($block)
    {
        $command = new GetBlockHeaderCommand(new SteemitWSConnector());

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $block);
        $content = $command->execute($commandQuery);

        return $content;
    }

    public static function getFollowers($account)
    {

        $command = new GetFollowersCommand(new SteemitHttpConnector());

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $account);
        $commandQuery->setParamByKey('1', '');
        $commandQuery->setParamByKey('2', 'blog');
        $commandQuery->setParamByKey('3', 1000);

        return $content = $command->execute($commandQuery);


    }

    public static function disconnect()
    {
        /*$connect = new SteemitWSConnector();
        $connect->destroyConnection();*/
        echo 321;
    }

    private static function checkResult($content, $f, $params = [])
    {
        if (isset($content['result'])) {
            return $content['result'];
        } else {
            self::disconnect();
            if (self::$attempt < 3) {
                AdminNotify::send('SteemApi #bots_project reconnect function:' . $f . ' attempt:' . self::$attempt);

                self::$attempt++;

                return call_user_func_array(array('self', $f), $params);
                //return self::$f();
            }
            AdminNotify::send('SteemApi #bots_project return false:' . $f . ' attempt:' . self::$attempt);

            return false;
        }
    }

    public static function GetDynamicGlobalProperties()
    {
        $content = '';
        try {
            $command = new GetDynamicGlobalPropertiesCommand(new SteemitHttpConnector());

            $commandQuery = new CommandQueryData();
            $content = $command->execute($commandQuery);
        } catch (Exception $e) {
            self::disconnect();

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

            $command = new GetBlockCommand(new SteemitHttpConnector());

            $content = $command->execute($commandQuery);
        } catch (Exception $e) {
            self::disconnect();

            return self::checkResult($content, 'getBlock', [$block_id]);
        }

        return self::checkResult($content, 'getBlock', [$block_id]);
    }
}