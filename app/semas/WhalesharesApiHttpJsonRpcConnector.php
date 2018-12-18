<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 24.10.2018
 * Time: 12:39
 */

namespace App\semas;


use GrapheneNodeClient\Connectors\Http\WhalesharesHttpJsonRpcConnector;

class WhalesharesApiHttpJsonRpcConnector extends WhalesharesHttpJsonRpcConnector
{

    /**
     * https or http server
     *
     * if you set several nodes urls, if with first node will be trouble
     * it will connect after $maxNumberOfTriesToCallApi tries to next node
     *
     * @var string
     */
    protected static  $nodeURL = [
        'https://wls.kennybll.com',
        //'http://188.166.99.136:19999',

    ];
    public function destroyConnection() {

    }
}