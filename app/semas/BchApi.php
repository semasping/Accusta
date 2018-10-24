<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 03.02.2018
 * Time: 17:23
 */

namespace App\semas;

use MongoDB;

class BchApi
{
    public static function getHistoryAccountFirst($acc)
    {
        if (getenv('BCH_API') == 'golos') {
            return GolosApi::getHistoryAccountFirst($acc);
        }
        if (getenv('BCH_API') == 'steemit') {
            return SteemitApi::getHistoryAccountFirst($acc);
        }
        if (getenv('BCH_API') == 'viz') {
            return VizApi::getHistoryAccountFirst($acc);
        }
    }


    public static function getTransaction($acc, $type)
    {
        if (getenv('BCH_API') == 'golos') {
            return GolosApi::getTransaction($acc, $type);
        }

        if (getenv('BCH_API') == 'steemit') {
            return SteemitApi::getTransaction($acc, $type);
        }

        if (getenv('BCH_API') == 'viz') {
            return VizApi::getTransaction($acc, $type);
        }

    }

    public static function convertToSg($gests)
    {
        if (getenv('BCH_API') == 'golos') {
            return GolosApi::convertToSg($gests);
        }

        if (getenv('BCH_API') == 'steemit') {
            return SteemitApi::convertToSg($gests);
        }

        if (getenv('BCH_API') == 'viz') {
            return VizApi::convertToSg($gests);
        }

    }

    public static function getHistoryAccountLast($acc)
    {
        if (getenv('BCH_API') == 'golos') {
            return GolosApi::getHistoryAccountLast($acc);
        }

        if (getenv('BCH_API') == 'steemit') {
            return SteemitApi::getHistoryAccountLast($acc);
        }

        if (getenv('BCH_API') == 'viz') {
            return VizApi::getHistoryAccountLast($acc);
        }
    }

    public static function getCurrentProcessedHistoryTranzId($acc)
    {
        if (getenv('BCH_API') == 'golos') {
            return GolosApi::getCurrentProcessedHistoryTranzId($acc);
        }

        if (getenv('BCH_API') == 'steemit') {

            return SteemitApi::getCurrentProcessedHistoryTranzId($acc);
        }

        if (getenv('BCH_API') == 'viz') {
            return VizApi::getCurrentProcessedHistoryTranzId($acc);
        }
    }

    public static function getCurrentProcessedHistoryTranzIdInDB($acc)
    {
        $current = 0;
        $max=0;
        $collection = self::getMongoDbCollection($acc);
        $filter = [];
        $options = ['sort' => ['_id' => -1]]; // -1 is for DESC
        $result = $collection->findOne($filter, $options);
        $max = $result['_id'];

        if (!is_numeric($max)){
            //запустить удаление или очистку от текста
            $collection->deleteMany(['_id' => ['$regex' => '[^0-9]*$']]);
            $max = 0;
        }
        $current = $collection->count();
        //dump($current, $max);
        if ($current == ($max) + 1) {
            return ($max);
        } else {
            //$collection->drop();
            AdminNotify::send('GetCurrentWithError(getCurrentProcessedHistoryTranzIdInDB) for account:'.$acc.' $max='.$max.' $current='.$current);
            return 0;
        }

    }

    public static function getMongoDbCollection($account)
    {
        if (getenv('BCH_API') == 'golos') {
            return (new MongoDB\Client)->selectCollection(getenv('BCH_API') . '_accusta', $account);
        }
        if (getenv('BCH_API') == 'viz') {
            return (new MongoDB\Client)->selectCollection(getenv('BCH_API') . '_accusta', $account);
        }
        if (getenv('BCH_API') == 'steemit') {
            return (new MongoDB\Client)->selectCollection('accusta', $account);
        }
    }



    public static function GetDynamicGlobalProperties()
    {
        if (getenv('BCH_API') == 'golos') {
            return GolosApi::GetDynamicGlobalProperties();
        }

        if (getenv('BCH_API') == 'steemit') {
            return SteemitApi::GetDynamicGlobalProperties();
        }

        if (getenv('BCH_API') == 'viz') {
            return VizApi::GetDynamicGlobalProperties();
        }
    }


    public static function getOpsInBlock($block_id)
    {
        if (getenv('BCH_API') == 'golos') {
            return GolosApi::getOpsInBlock($block_id);
        }

        if (getenv('BCH_API') == 'steemit') {
            return SteemitApi::getOpsInBlock($block_id);
        }

        if (getenv('BCH_API') == 'viz') {
            return VizApi::getOpsInBlock($block_id);
        }
    }

    public static function getPost($author, $permlink)
    {
        echo 'GetPost for '.$author . '/'. $permlink . "\n";
        if (getenv('BCH_API') == 'golos') {
            return GolosApi::getContent($author,$permlink);
        }

        if (getenv('BCH_API') == 'steemit') {
            return SteemitApi::getContent($author,$permlink);
        }

        if (getenv('BCH_API') == 'viz') {
            return VizApi::getContent($author,$permlink);
        }
    }

    public static function getFullAccount($account)
    {

        if (getenv('BCH_API') == 'golos') {
            return GolosApi::getAccountFull($account);
        }

        if (getenv('BCH_API') == 'steemit') {
            return SteemitApi::getAccountFull($account);
        }

        if (getenv('BCH_API') == 'viz') {
            return VizApi::getAccountFull($account);
        }
    }
}