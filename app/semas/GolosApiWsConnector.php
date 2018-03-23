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
    //protected $nodeURL = 'wss://ws.golos.io';
    protected $nodeURL = ['wss://ws.golos.io','wss://api.golos.cf'];
    //protected $nodeURL = 'wss://api.golos.cf';



    public function destroyConnection() {
        //self::$connection = null;
    }

    /**
     * @param string $apiName
     * @param array  $data
     * @param string $answerFormat
     * @param int    $try_number Try number of getting answer from api
     *
     * @return array|object
     * @throws ConnectionException
     * @throws \WebSocket\BadOpcodeException
     */
    public function doRequest($apiName, array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY, $try_number = 1)
    {
        $requestId = $this->getNextId();
        $data = [
            'jsonrpc' => 2.0,
            'id'     => $requestId,
            'method' => 'call',
            'params' => [
                $apiName,
                $data['method'],
                $data['params']
            ]
        ];
        try {
            $connection = $this->getConnection();
            $connection->send(json_encode($data));

            $answerRaw = $connection->receive();
            $answer = json_decode($answerRaw, self::ANSWER_FORMAT_ARRAY === $answerFormat);

            //check that answer has the same id or id from previous tries, else it is answer from other request
            if (self::ANSWER_FORMAT_ARRAY === $answerFormat) {
                $answerId = $answer['id'];
            } elseif (self::ANSWER_FORMAT_OBJECT === $answerFormat) {
                $answerId = $answer->id;
            }
            if ($requestId - $answerId > ($try_number - 1)) {
                throw new ConnectionException('get answer from old request');
            }


        } catch (ConnectionException $e) {

            if ($try_number < $this->maxNumberOfTriesToCallApi) {
                //if got WS Exception, try to get answer again
                $answer = $this->doRequest($apiName, $data, $answerFormat, $try_number + 1);
            } elseif ($this->isExistReserveNodeUrl()) {
                //if got WS Exception after few ties, connect to reserve node
                $this->connectToReserveNode();
                $answer = $this->doRequest($apiName, $data, $answerFormat);
            } else {
                //if nothing helps
                throw $e;
            }
        }

        return $answer;
    }

}