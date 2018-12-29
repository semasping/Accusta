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

class WhalesharesApiWsConnector extends WSConnectorAbstract
{

    /**
     * wss or ws server
     *
     * @var string
     */
    protected static $nodeURL = [
        //'ws://80.241.216.146:8090',// не работаю никакие варианты
        'wss://api.golos.blckchnd.com/ws',
        //'wss://api.golos.cf',
        //'wss://ws.golos.io',
        //'wss://ws.golos.blog',
        //'wss://17.golos.cf',


        //'wss://ws17.golos.io',

    ];



    public function destroyConnection() {
        self::$connection = null;
    }


}