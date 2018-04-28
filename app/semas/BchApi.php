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
    }


    public static function getTransaction($acc, $type)
    {
        if (getenv('BCH_API') == 'golos') {
            return GolosApi::getTransaction($acc, $type);
        }

        if (getenv('BCH_API') == 'steemit') {
            return SteemitApi::getTransaction($acc, $type);
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

    }

    public static function getHistoryAccountLast($acc)
    {
        if (getenv('BCH_API') == 'golos') {
            return GolosApi::getHistoryAccountLast($acc);
        }

        if (getenv('BCH_API') == 'steemit') {
            return SteemitApi::getHistoryAccountLast($acc);
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
        /*if (intval($max))*/
        $current = $collection->count();
        //dump($current);
        if ($current == intval($max) + 1) {
            return intval($max);
        } else {
            AdminNotify::send('GetCurrentWithError(getCurrentProcessedHistoryTranzIdInDB) for account:'.$acc.' $max='.$max.' $current='.$current);
            return 0;
        }

    }

    public static function getMongoDbCollection($account)
    {
        if (getenv('BCH_API') == 'golos') {
            return (new MongoDB\Client)->selectCollection(getenv('BCH_API') . '_accusta', $account);
        }
        if (getenv('BCH_API') == 'steemit') {
            return (new MongoDB\Client)->selectCollection('accusta', $account);
        }
    }
}