##  PHP爬虫：Selenium + 代理池

### 运行环境
* CentOS 7.2
* PHP7.2
* Swoole 4.3.5
* EasySwoole 3.1.18
* Google Chrome 78.0.3904.108
* ChromeDriver 78.0.3904.105

### 核心包
* facebook/webdriver

### 主要模块
* 定时更新代理池（自定义进程）
* 定时爬取列表（自定义进程）
* 主进程查询列表项，将每一项丢给异步任务，异步任务使用谷歌浏览器+代理爬取对应数据
