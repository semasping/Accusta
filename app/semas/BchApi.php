<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 03.02.2018
 * Time: 17:23
 */

namespace App\semas;


class BchApi
{
    public static function getHistoryAccountFirst($acc)
    {
        if (getenv('BCH_API') == 'golos')
            return GolosApi::getHistoryAccountFirst($acc);
        if (getenv('BCH_API') == 'steemit')
            return SteemitApi::getHistoryAccountFirst($acc);
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
}