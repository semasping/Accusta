<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 29.05.2018
 * Time: 15:47
 */

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


/**
 * @property int $_id
 * @property int $id
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class BchBlock extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'blocks';
}