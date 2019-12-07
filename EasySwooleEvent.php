<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Lib\Kv;
use App\Process\ListSpider;
use App\Spider\ItemSpider;
use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        //更新代理池进程
        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\Process\UpdateProxyPool('UpdateProxyPool', []))->getProcess());
        //列表爬取进程
        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\Process\ListSpider('ListSpider', []))->getProcess());

        $register->set($register::onWorkerStart,function(\swoole_server $server,$workerId){
            if($workerId == 0)
            {
                Timer::getInstance()->loop(30000, function () {
                    $ret = Kv::redis()->get(ListSpider::LIST_KV_KEY);
                    if($ret){
                        $ret = json_decode($ret,true);
                        foreach($ret as $item) {
                            TaskManager::async(function () use($item){
                                (new ItemSpider(true))->run($item);
                                return true;
                            }, function () use($item){
                                var_dump("{$item} Done");
                            });
                        }
                    }
                });
            }
        });

    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}