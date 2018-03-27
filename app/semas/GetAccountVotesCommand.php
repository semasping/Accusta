<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetAccountVotesCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_account_votes';

    /** @var string */
    protected $apiName = 'social_network ';

    /** @var array */
    protected $queryDataMap = [
        '0' => ['string'], //account name
    ];
}