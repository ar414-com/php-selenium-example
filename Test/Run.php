<?php
/**
 * Created by PhpStorm.
 * User: Ar414.com@gmail.com
 * Date: 2019/12/15
 * Time: 20:00
 */

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once('vendor/autoload.php');

header("Content-Type: text/html; charset=UTF-8");

$waitSeconds = 15;  //需等待加载的时间，一般加载时间在0-15秒，如果超过15秒，报错。
$host = 'http://localhost:4444/wd/hub'; // this is the default
$capabilities = DesiredCapabilities::chrome();
$driver = RemoteWebDriver::create($host, $capabilities, 5000);

try
{
    $driver->get('https://www.baidu.com/');
    echo "当前页面标题：".$driver->getTitle()."\n";
    //定位到输入框->输入"新浪微博"->点击搜索
    $driver->findElement(WebDriverBy::id('kw'))->sendKeys('新浪微博')->submit();
    // 等待新的页面加载完成....
    $driver->wait($waitSeconds)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(
            WebDriverBy::partialLinkText('新浪微博')
        )
    );

    //保存当前页面
    savePageSource($driver,1);

    //一般点击链接的时候，担心因为失去焦点而抛异常，则可以先调用一下sendKeys，再click
    $driver->findElement(WebDriverBy::partialLinkText('新浪微博'))->sendKeys('xxx')->click();

    switchToEndWindow($driver); //切换至最后一个window

    $driver->wait($waitSeconds)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(
            WebDriverBy::cssSelector('h2.UG_box_title')
        )
    );
    echo "当前页面标题：".$driver->getTitle()."\n";
    //保存当前页面
    savePageSource($driver,2);

    $driver->findElement(WebDriverBy::cssSelector('input.W_input'))->sendKeys('周杰伦');
    $driver->findElement(WebDriverBy::cssSelector('a.W_ficon.ficon_search.S_ficon'))->sendKeys('xxx')->click();

    $driver->wait($waitSeconds)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(
            WebDriverBy::cssSelector('div.info')
        )
    );
    echo "当前页面标题：".$driver->getTitle()."\n";
    //保存当前页面
    savePageSource($driver,3);
    //获取相关热门
    //div.card-wrap
    $elements = $driver->findElements(WebDriverBy::cssSelector('div.card-wrap'));
    foreach($elements as $index => $element)
    {
        $userNickName = $element->findElement(WebDriverBy::cssSelector('a.name'))->getText();
        echo "用户名：$userNickName\n";
        $content = $element->findElement(WebDriverBy::cssSelector('p.txt'))->getText();
        echo "内容：{$content} \n";
        echo "------------------------分割线------------------------\n";
    }
    //关闭标签并退出浏览器
    $driver->close();
    $driver->quit();
}
catch (\Throwable $throwable)
{
    $driver->close();
    $driver->quit();
    var_dump($throwable->getMessage());
}

/**
 * 保存页面
 * @param $driver
 * @param $pageNumber
 */
function savePageSource($driver,$pageNumber)
{
    //获取页面资源
    $pageSource = $driver->getPageSource();
    //输入到文件
    file_put_contents("./ar414_page{$pageNumber}.html",$pageSource);
}


/**
 * 切换至最后一个window
 * 因为有些网站的链接点击过去带有target="_blank"属性，
 * 就新开了一个TAB，而selenium还是定位到老的TAB上，
 * 如果要实时定位到新的TAB，则需要调用此方法，
 * 切换到最后一个window
 * @param $driver
 */
function switchToEndWindow($driver){

    $arr = $driver->getWindowHandles();
    foreach ($arr as $k=>$v){
        if($k == (count($arr)-1)){
            $driver->switchTo()->window($v);
        }
    }
}