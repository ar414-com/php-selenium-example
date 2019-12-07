<?php
/**
 * Created by PhpStorm.
 * User: ljx@dotamore.com
 * Date: 2019/12/7
 * Time: 22:03
 */

namespace App\Lib;


use App\Process\UpdateProxyPool;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\WebDriverPlatform;

class ChromeDriver
{
    const WAIT_SECONDS = 60;
    const LOCATION_GOOGLE_HOST = 'http://localhost:4444/wd/hub';

    private $driver;

    public function __construct($isProxy = false)
    {
        $useragent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0C; .NET4.0E)";
        $options = new ChromeOptions();
        $options->addArguments(["user-agent={$useragent}"]);
        $capabilitiesOptions = [
            WebDriverCapabilityType::BROWSER_NAME => WebDriverBrowserType::CHROME,
            WebDriverCapabilityType::PLATFORM => WebDriverPlatform::ANY,
            ChromeOptions::CAPABILITY => $options
        ];
        if($isProxy)
        {
            $proxy = Kv::redis()->lPop(UpdateProxyPool::PROXY_KV_KEY);
            //https： 'https://'.$proxy;
            //socks4：'socks4://'.$proxy;
            $proxy = 'socks5://'.$proxy;
            $capabilitiesOptions[WebDriverCapabilityType::PROXY] = ['proxyType' => 'manual', 'httpProxy' => $proxy, 'sslProxy' => $proxy];
        }
        $capabilities = new DesiredCapabilities($capabilitiesOptions);
        $this->driver = RemoteWebDriver::create(self::LOCATION_GOOGLE_HOST, $capabilities, 5000);
    }

    /**
     * @return RemoteWebDriver
     */
    public function getDriver()
    {
        return $this->driver;
    }
}