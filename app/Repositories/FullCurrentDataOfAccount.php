<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 13.09.2018
 * Time: 13:15
 */

namespace App\Repositories;


use App\semas\BchApi;
use Illuminate\Filesystem\Cache;

class FullCurrentDataOfAccount
{
    public static function get($account)
    {
        return Cache::remember('fulldata' . $account, 10, function () use ($account) {
            return BchApi::getFullAccount($account);
        });
    }
}