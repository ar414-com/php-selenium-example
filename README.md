##  PHP爬虫：Selenium + 代理池

### 运行环境
* CentOS 7.2
* PHP7.2
* Swoole 4.3.5
* EasySwoole 3.1.18
* Google Chrome 78.0.3904.108
* ChromeDriver 78.0.3904.105

### 代理
* 站大爷

### 核心包
* facebook/webdriver

### 主要模块
* 定时更新代理池（自定义进程）
* 定时爬取列表（自定义进程）
* 主进程查询列表项，将每一项丢给异步任务，异步任务使用谷歌浏览器+代理爬取对应数据

### 运行
````
//后台启动 Selenium Server
#nohup  java -jar selenium-server-standalone-3.141.59.jar  &

#git clone https://github.com/ar414-com/phpseleniumdemo.git && cd ./phpseleniumdemo

#composer install

#php ./vendor/easyswoole/easyswoole/bin/easyswoole install
EasySwooleEvent.php has already existed, do you want to replace it? [ Y / N (default) ] : N
dev.php has already existed, do you want to replace it? [ Y / N (default) ] : N
produce.php has already existed, do you want to replace it? [ Y / N (default) ] : N

//修改App\Process\UpdateProxyPool，改成爬取你代理的逻辑，这里使用的是站大爷代理 提取链接马赛克了
//运行
#php easyswoole start



````