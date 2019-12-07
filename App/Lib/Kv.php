<?php
/**
 * Created by PhpStorm.
 * User: ljx@dotamore.com
 * Date: 2019/12/7
 * Time: 22:05
 */

namespace App\Lib;


class Kv
{
    /**
     * @return \Redis
     */
    public static function redis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        return $redis;
    }
}