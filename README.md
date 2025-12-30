# PHP Web Crawler: Selenium + Proxy Pool

## Overview

This project demonstrates how to build a **PHP-based web crawler using Selenium with a proxy pool**, specifically designed to handle websites with **strong anti-scraping mechanisms**.

> ⚠️ **Disclaimer**  
> This project is for **learning and communication purposes only**.  
> **Do NOT** use it for any activities that violate local laws or regulations.  
> All usage must comply with applicable **Cybersecurity Laws** and related legal requirements.

---

## Tutorials

Step-by-step guides (Chinese documentation):

- [【PhpSelenium】1. Environment Setup](https://ar414-com.github.io/PhpSelenium/2-1/)
- [【PhpSelenium】2. Basic Usage](https://ar414-com.github.io/PhpSelenium/2-2/)
- [【PhpSelenium】3. Scheduled Crawlers + Multi-Task Crawlers + Proxy Pool](https://ar414-com.github.io/PhpSelenium/2-3/)

---

## Runtime Environment

| Component | Version |
|---------|--------|
| OS | CentOS 7.2 |
| PHP | 7.2 |
| Swoole | 4.3.5 |
| EasySwoole | 3.1.18 |
| Google Chrome | 78.0.3904.108 |
| ChromeDriver | 78.0.3904.105 |
| Selenium Server | 3.141.59 |

---

## Core Dependency

- **facebook/webdriver**

Used to control Google Chrome through Selenium WebDriver.

---

## Architecture & Main Modules

- **Proxy Pool Updater**  
  A custom EasySwoole process that periodically updates the proxy pool.

- **Scheduled List Crawler**  
  A custom process that periodically crawls list pages.

- **Main Process**  
  Fetches list items and dispatches each item to an asynchronous task.

- **Async Task Workers**  
  Each task launches a Chrome browser with a proxy enabled to crawl detailed data.

---

## Installation & Execution

### 1. Start Selenium Server (Background)

```bash
nohup java -jar selenium-server-standalone-3.141.59.jar &
```

---

### 2. Clone the Project

```bash
git clone https://github.com/ar414-com/phpseleniumdemo.git
cd phpseleniumdemo
```

---

### 3. Install Dependencies

```bash
composer install
```

---

### 4. Install EasySwoole Framework

```bash
php ./vendor/easyswoole/easyswoole/bin/easyswoole install
```

When prompted, select **N** for existing files:

```text
EasySwooleEvent.php has already existed, do you want to replace it? [Y/N]: N
dev.php has already existed, do you want to replace it? [Y/N]: N
produce.php has already existed, do you want to replace it? [Y/N]: N
```

---

### 5. Configure Proxy Pool Logic

Edit the following file:

```
App/Process/UpdateProxyPool.php
```

Replace the proxy-fetching logic with your own proxy source.  
(The demo uses a third-party proxy service; the extraction URL is intentionally masked.)

---

### 6. Start the Service

```bash
php easyswoole start
```

---

## Notes

- Suitable for websites with **strong anti-bot protections**
- Selenium + real browser + proxy rotation greatly improves crawl success rate
- Recommended for **learning, research, and controlled internal use**

---

## License

For learning and communication purposes only.  
Please comply with all applicable laws and regulations.
