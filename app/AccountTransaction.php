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
 */
class AccountTransaction extends Eloquent
{
    protected $connection = 'mongodb';


}