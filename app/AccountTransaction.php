<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 20.02.2018
 * Time: 13:15
 */

namespace App;

use Jenssegers\Date\Date;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


/**
 * @property int $_id
 * @property Date $created_at
 * @property Date $updated_at
 * @property string account
 * @property  string trx_id
 * @property  string block
 * @property  string timestamp
 * @property  array op
 * @property  string type
 */
class AccountTransaction extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'account_transactions';

/*    public function __construct($acc, array $attributes = [])
    {
        parent::__construct($attributes);
        //dump($acc,$attributes);
        $this->collection = 'account_transactions_'.$acc;
    }*/

    /**
     * @param string $collection
     */
   /* public static function setCollection(string $collection)
    {
        self::$collection = 'account_transactions_'.$collection;
    }*/
    /*    public $account;
        public $trx_id;
        public $block;
        public $timestamp;
        public $op;
        public $type;*/


}