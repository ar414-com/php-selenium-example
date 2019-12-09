<?php
/**
 * Created by PhpStorm.
 * User: ar414.com@gmail.com
 * Date: 2019/12/7
 * Time: 22:01
 */

namespace App\Process;

use App\Lib\ChromeDriver;
use App\Lib\Kv;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Logger;

class ListSpider extends AbstractProcess
{
    const API = 'https://www.188-sb.com/SportsBook.API/web?lid=1&zid=3&pd=%23AC%23B151%23C1%23D50%23E10%23F163%23&cid=42&ctid=42';

    const LIST_KV_KEY = 'spider:list';
    const TIMER = 20; //20秒执行一次

    public function run($arg)
    {
        while (true)
        {
            try
            {
                $driver = (new ChromeDriver(true))->getDriver();
                $driver->get(self::API);
                $listStr = $driver->getPageSource();
                var_dump($listStr);
                file_put_contents("/www/wwwroot/blog/phpseleniumdemo/listStr.html",$listStr);
                preg_match_all("/PD=(.*);/U",$listStr,$list);
                $list = array_unique($list[1]);
                if($list)
                {
                    Kv::redis()->set(self::LIST_KV_KEY,json_encode($list));
                }
                var_dump('done');
                $driver->close();
                $driver->quit();
            }
            catch (\Throwable $throwable)
            {
                $driver->close();
                $driver->quit();
                Logger::getInstance()->log($throwable->getMessage(),'ListSpiderError');
                var_dump($throwable->getMessage());
            }
            sleep(self::TIMER);
        }

    }
}