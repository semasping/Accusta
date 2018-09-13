<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 14.11.2017
 * Time: 10:39
 */

namespace App\semas;


use GrapheneNodeClient\Connectors\WebSocket\WSConnectorAbstract;
use WebSocket\ConnectionException;

class GolosApiWsConnector extends WSConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_GOLOS;

    /**
     * wss or ws server
     *
     * @var string
     */
    protected static $nodeURL = [
        //'ws://80.241.216.146:8090',// не работаю никакие варианты

        'wss://api.golos.cf',

        'wss://ws.golos.blog',
        'wss://17.golos.cf',

        //'wss://ws.golos.io',
        //'wss://ws17.golos.io',

    ];
    //protected static $nodeURL = ['wss://ws.golos.io','wss://api.golos.cf'];
    //protected $nodeURL = 'wss://api.golos.cf';
    //protected static $nodeURL = 'ws://80.241.216.146:8090';// не работаю никакие варианты



    public function destroyConnection() {
        self::$connection = null;
    }


}