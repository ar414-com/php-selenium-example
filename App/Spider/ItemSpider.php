<?php
/**
 * Created by PhpStorm.
 * User: ljx@dotamore.com
 * Date: 2019/12/7
 * Time: 22:35
 */

namespace App\Spider;


use App\Lib\ChromeDriver;
use EasySwoole\EasySwoole\Logger;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class ItemSpider
{
    public function run($itemPath)
    {
        $driver = (new ChromeDriver(true))->getDriver();
        $itemPath = str_replace('#','/',$itemPath);
        $url = "https://www.188-sb.com/#{$itemPath}";
        var_dump($url);
        try
        {
            $driver->get($url);
            $driver->wait(ChromeDriver::WAIT_SECONDS)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(
                    WebDriverBy::className('gl-MarketGroupButton_Text')
                )
            );
            Logger::getInstance()->console("The title is '" . $driver->getTitle() . "'\n");
            Logger::getInstance()->console("The current URI is '" . $driver->getCurrentURL() . "'\n");
            $body = $driver->getPageSource();
            var_dump($body);
            $driver->close();
            $driver->quit();
            //TODO 清洗数据 入库
        }
        catch (\Throwable $throwable)
        {
            Logger::getInstance()->log($throwable->getMessage(),'Bet365ApiRun');
            $driver->close();
            $driver->quit();
        }
        return;
    }
}