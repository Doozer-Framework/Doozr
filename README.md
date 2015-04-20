<img src="https://avatars2.githubusercontent.com/u/514566?v=3&u=4615dfc4970d93dea5d3eaf996b7903ee6e24e20&s=140" align="right" />
---
![Logo of DoozR](docs/logo-large.png)  
The **lightweight** PHP-Framework for **high-performance** websites  

| [![Build Status](https://travis-ci.org/clickalicious/DoozR.svg?branch=master)](https://travis-ci.org/clickalicious/DoozR) 	| [![Scrutinizer](https://img.shields.io/scrutinizer/g/clickalicious/DoozR.svg)](https://scrutinizer-ci.com/g/clickalicious/DoozR/) 	| [![clickalicious premium](https://img.shields.io/badge/clickalicious-premium-green.svg?style=flat)](https://www.clickalicious.de/) 	| [![Packagist](https://img.shields.io/packagist/l/clickalicious/DoozR.svg?style=flat)](http://opensource.org/licenses/BSD-3-Clause) 	|
|---	|---	|---	|---	|
| [![GitHub issues](https://img.shields.io/github/issues/clickalicious/doozr.svg?style=flat)](https://github.com/clickalicious/DoozR/issues) 	| [![Coverage Status](https://coveralls.io/repos/clickalicious/DoozR/badge.svg)](https://coveralls.io/r/clickalicious/DoozR)  	| [![GitHub release](https://img.shields.io/github/release/clickalicious/DoozR.svg?style=flat)](https://github.com/clickalicious/DoozR/releases) 	| [![GitHub stars](https://img.shields.io/github/stars/clickalicious/doozr.svg?style=flat)](https://github.com/clickalicious/DoozR/stargazers)  	|


## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Philosophy](#philosophy)
- [Services](#services)
- [API](#api)
- [Versioning](#versioning)
- [Roadmap](#roadmap)
- [Security-Issues](#security-issues)
- [Install »](https://github.com/clickalicious/DoozR/blob/master/docs/INSTALL.md)
- [Architecture »](https://github.com/clickalicious/DoozR/blob/master/docs/ARCHITECTURE.md)
- [Service: I18n »](https://github.com/clickalicious/DoozR/blob/master/lib/Service/DoozR/I18n/README.md)
- [License »](LICENSE)


## Features

 - High performance (developed with profiler)
 - Ultra fast routing, caching and request processing!
 - Lightweight
 - Integrated logger subsystem
 - Debugging-Tools/Support
 - Stable + High-Quality
 - Clean & well documented code
 - No Database-Framework bundled (free choice)


## Requirements

 - PHP >= 5.4 (compatible up to version 5.6)


## Philosophy

DoozR is the **lightweight** PHP-Framework for **high-performance** websites. It follows the `convention over configuration`-priciniple. `DoozR` comes with the right set of core services. Build, test and deploy **high-scalable** applications with in very short time. `DoozR` supports the **CLI**, **CGI** and PHP's **Internal Webserver** (for development) SAPI. A powerful routing mechanism with builtin `I18n` support empowers you to build internationalized applications. But **DoozR** has so much more to offer!


## Services

`DoozR` is shipped with the following high quality core services:

 - `Acl` (CRUD Secured Objects)
 - `Cache` (Interface to Filesystem + Memcached)
 - `Config` (Configreader mit Caching support for INI, JSON)
 - `Crypt` (Secure En-/Decryption)
 - `Filesystem` (Filesystem wrapper with virtual FS support)
 - `Form` (HTML5 secure form handler + validation)
 - `Http` (Wrapper to CURL)
 - `I18n` (Internationalization + Localization support with gettext emulation)
 - `Password` (Password generator + validator)
 - `Session` (Secure and OO Session interface)
 - `Template` (Interface to PHPTal)

100% `composer` support. For all missing features we can make use of [packagist.org][1]. So just put the required package(s) in your `composer.json` and use them right after installation without need to include `.../vendor/autoload.php` manually. `DoozR` detects the `composer` packages and includes the autoloader of `composer`.


## API

`DoozR` provides a ready to use `API` right after installation. The structure and routes are generic and built using best practices from community powered PHP-projects.


## Versioning

For a consistent versioning i decided to make use of `Semantic Versioning 2.0.0` http://semver.org. Its easy to understand, very common and known from many other software projects.


## Roadmap

- [ ] Target stable release 1.0.0 (The Rock)
- [ ] 75 - 90% Test Coverage
- [ ] Scrutinizer and Travis implementation
- [ ] Bug hunt and quality offensive
- [ ] Documentation


## Security Issues

If you encounter a (potential) security issue don't hesitate to get in contact with me `opensource@clickalicious.de` before releasing it to the public. So i get a chance to prepare and release an update before the issue is getting shared. Thank you!


## Participate & Share

... yeah. If you're a code monkey too - maybe we can build a force ;) If you would like to participate in either **Code**, **Comments**, **Documentation**, **Wiki**, **Bug-Reports**, **Unit-Tests**, **Bug-Fixes**, **Feedback** and/or **Critic** then please let me know as well!
<a href="https://twitter.com/intent/tweet?hashtags=&original_referer=http%3A%2F%2Fgithub.com%2F&text=DoozR%20-%20The%20lightweight%20PHP-Framework%20for%20high-performance%20projects%20%40phpfluesterer%20%23DoozR%20%23php%20https%3A%2F%2Fgithub.com%2Fclickalicious%2FDoozR&tw_p=tweetbutton" target="_blank">
  <img src="http://jpillora.com/github-twitter-button/img/tweet.png"></img>
</a>

## Sponsors

Thanks to our sponsors and supporters:  

| JetBrains | Navicat |
|---|---|
| <a href="https://www.jetbrains.com/phpstorm/" title="PHP IDE :: JetBrains PhpStorm" target="_blank"><img src="https://www.jetbrains.com/phpstorm/documentation/docs/logo_phpstorm.png"></img></a> | <a href="http://www.navicat.com/" title="Navicat GUI - DB GUI-Admin-Tool for MySQL, MariaDB, SQL Server, SQLite, Oracle & PostgreSQL" target="_blank"><img src="http://upload.wikimedia.org/wikipedia/en/9/90/PremiumSoft_Navicat_Premium_Logo.png" height="55" /></a>  |


[1]: https://packagist "packagist.org - Package registry of composer"
