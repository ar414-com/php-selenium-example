<?php
/**
 * Created by PhpStorm.
 * User: ljx@dotamore.com
 * Date: 2019/12/7
 * Time: 21:00
 */

namespace App\Process;


use App\Lib\Curl;
use EasySwoole\Component\Process\AbstractProcess;

class UpdateProxyPool extends AbstractProcess
{

    private $proxyListApi = "http://www.zdopen.com/ShortS5Proxy/GetIP/?api=%s&akey=%s&order=2&type=3";
    const PROXY_KV_KEY = 'spider:proxy:list';
    const TIMER = 20; //20秒执行一次

    protected function initProxyListApi()
    {
//        $this->proxyListApi = sprintf($this->proxyListApi,$_ENV['PROXY_LIST_API'],$_ENV['PROXY_LIST_KEY']);
        $this->proxyListApi = sprintf($this->proxyListApi,20191231231237085,'72axxxae0fe34');
    }

    public function run($arg)
    {
        $this->initProxyListApi();
        //依赖 composer require easyswoole/curl=1.0.1
        while (true)
        {
            $ret = Curl::get($this->proxyListApi);
            var_dump($ret);
            if($ret) {
                $ret = json_decode($ret,true);
                if($ret['code'] == 10001 && isset($ret['data']['proxy_list']) && !empty($ret['data']['proxy_list']) ) {
                    foreach($ret['data']['proxy_list'] as $proxy) {
                        $proxyItem = $proxy['ip'] . ':'.$proxy['port'];
                        $this->getRedis()->lPush(self::PROXY_KV_KEY,$proxyItem);
                    }
                }
            }
            sleep(self::TIMER);
        }

    }

    /**
     * 方便演示，一般会封装一个KV类
     * @return \Redis
     */
    protected function getRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        return $redis;
    }

}