<?php


namespace app\semas;


use GrapheneNodeClient\Commands\DataBase\CommandAbstract;

class GetContentCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_content';

    /** @var string */
    protected $apiName = 'social_network';

    /** @var array */
    protected $queryDataMap = [
        '0' => ['string'], //author
        '1' => ['string'], //permlink
    ];
}