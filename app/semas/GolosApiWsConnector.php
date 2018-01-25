<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 14.11.2017
 * Time: 10:39
 */

namespace App\semas;


use GrapheneNodeClient\Connectors\WebSocket\WSConnectorAbstract;

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
    protected $nodeURL = 'wss://ws.golos.io';
    //protected $nodeURL = 'wss://api.golos.cf';



    public function destroyConnection() {
        self::$connection = null;
    }

}