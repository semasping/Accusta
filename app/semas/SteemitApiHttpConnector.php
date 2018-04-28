<?php

namespace App\semas;



use GrapheneNodeClient\Connectors\Http\HttpConnectorAbstract;

class SteemitApiHttpConnector extends HttpConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_STEEMIT;

    /**
     * https or http server
     *
     * if you set several nodes urls, if with first node will be trouble
     * it will connect after $maxNumberOfTriesToCallApi tries to next node
     *
     * @var string
     */
    protected $nodeURL = [
        'http://rpc.curiesteem.com',
        'https://api.steemit.com',
        'https://steemd.privex.io',

    ];
}