<img src="https://avatars2.githubusercontent.com/u/514566?v=3&u=4615dfc4970d93dea5d3eaf996b7903ee6e24e20&s=140" align="right" />
---
![Logo of Doozr](docs/logo-large.png)

Doozr: The **lightweight** PHP-Framework for **high-performance** websites.

| [![Build Status](https://img.shields.io/travis/clickalicious/Doozr.svg)](https://travis-ci.org/clickalicious/Doozr) 	| [![Scrutinizer](https://img.shields.io/scrutinizer/g/clickalicious/Doozr.svg)](https://scrutinizer-ci.com/g/clickalicious/Doozr/) 	| [![Code Coverage](https://scrutinizer-ci.com/g/clickalicious/Doozr/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/clickalicious/Doozr/?branch=master) 	| [![clickalicious open source](https://img.shields.io/badge/clickalicious-open--source-green.svg?style=flat)](https://www.clickalicious.de/) 	|
|---	|---	|---	|---	|
| [![GitHub release](https://img.shields.io/github/release/clickalicious/Doozr.svg?style=flat)](https://github.com/clickalicious/Doozr/releases) 	| [![Waffle.io](https://img.shields.io/waffle/label/clickalicious/Doozr/in%20progress.svg)](https://waffle.io/clickalicious/Doozr) 	| [![SensioLabsInsight](https://insight.sensiolabs.com/projects/ee43ca73-2756-4f97-8054-57cd5c98c394/mini.png)](https://insight.sensiolabs.com/projects/ee43ca73-2756-4f97-8054-57cd5c98c394) 	| [![Packagist](https://img.shields.io/packagist/l/clickalicious/Doozr.svg?style=flat)](http://opensource.org/licenses/BSD-3-Clause)  	|


## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Philosophy](#philosophy)
- [Services](#services)
- [Versioning](#versioning)
- [Roadmap](#roadmap)
- [Security-Issues](#security-issues)
- [Install »](https://github.com/clickalicious/Doozr/blob/master/docs/INSTALL.md)
- [Architecture »](https://github.com/clickalicious/Doozr/blob/master/docs/ARCHITECTURE.md)
- [Service: Cache »](https://github.com/clickalicious/Doozr/blob/master/src/Service/Doozr/Cache/README.md)
- [Service: Configuration »](https://github.com/clickalicious/Doozr/blob/master/src/Service/Doozr/Configuration/README.md)
- [Service: Crypt »](https://github.com/clickalicious/Doozr/blob/master/src/Service/Doozr/Crypt/README.md)
- [Service: Filesystem »](https://github.com/clickalicious/Doozr/blob/master/src/Service/Doozr/Filesystem/README.md)
- [Service: Form »](https://github.com/clickalicious/Doozr/blob/master/src/Service/Doozr/Form/README.md)
- [Service: I18n »](https://github.com/clickalicious/Doozr/blob/master/src/Service/Doozr/I18n/README.md)
- [Service: Password »](https://github.com/clickalicious/Doozr/blob/master/src/Service/Doozr/Password/README.md)
- [Service: Session »](https://github.com/clickalicious/Doozr/blob/master/src/Service/Doozr/Session/README.md)
- [Service: Template »](https://github.com/clickalicious/Doozr/blob/master/src/Service/Doozr/Template/README.md)
- [License »](LICENSE)


## Features

 - High performance (developed using [profiler][5])
 - Ultra [fast routing][4], caching and request processing
 - Lightweight and high quality code base (following *PSR-[1][6],[2][7],[3][8],[4][9],[7][10]*)
 - Fully [*PSR-7*][10] compatible
 - Middleware [Layer][11] support
 - Integrated [logging][8] subsystem
 - Clean & [well documented][12] code
 - Unit-tested
 - Shipped with implemented [profiler][5] 


## Requirements

 - `PHP >= 5.6` (compatible up to PHP version 7.x *currently* NOT compatible with `HHVM`)


## Philosophy

`Doozr` is the **lightweight** PHP-Framework for **high-performance** applications. It follows the `convention over configuration`-principle. `Doozr` comes with the right set of core *Services* to build, test and deploy **high-scalable** and **stable** web-applications. `Doozr` supports the **CLI**, **CGI** as well as PHP's **Internal Webserver** *SAPI*. A ultra fast routing mechanism helps you building flexible web-applications. But **Doozr** has so much more to offer! Try it, run it ... ♥ it ;)


## Services

`Doozr` is shipped with the following Services:

 - `Cache` ([PSR-6][2] compatible caching subsystem, support for Filesystem, Memcached and ...)
 - `Configuration` (Configuration-Reader for Ini- & JSON-Configurations and caching)
 - `Crypt` (AES secure en-/decryption)
 - `Filesystem` (Filesystem wrapper with virtual filesystem support)
 - `Form` (Secure form handler (csrf/token) + validation)
 - `I18n` (Internationalization + Localization, native gettext™ support & gettext emulation)
 - `Password` (Password generator + validationHandler, support for PHPass based hashes)
 - `Session` (Secure OOP Facade, En-/Decryption, Unique Identifier per User, interval regenerating, ...)
 - `Template` (Interface to PHPTal with full PHPTal templating support)

100% `composer` support. For any missing features we can make use of [packagist.org][1]. So just put the required package(s) in your `composer.json` and use them right after installation without need to include `.../vendor/autoload.php` manually. `Doozr` detects the `composer` packages and includes the autoloader of `composer`.

This mechanism is our pragmatic approach as bridge to all the software & libraries out there. Of course you can build your very own *Services* and load them the same way as you would load a `Doozr` *Service* - but you are not required to do so. In other words: If you just want to use some smart library then pick it by using `composer` - but if you want to build your own piece of library then you should build a `Doozr` *Service* (which itself can use Composer as well).


## Testing & Coverage

Our minimum quality standard for releasing `Doozr` version 1.0.0 is a test coverage of ~90%. Currently - while focussing the first public beta release of Doozr - we target a test coverage of ~75%. But we will do our best to reach the 90% as marker of excellence. It's hard work currently cleaning the whole code base, refactoring huge parts and removing some uneccessary ones. We know how important it is to provide stable and tested functionality - So you can always track the coverage by viewing [Doozr's online code coverage report in HTML-format][3].


## Versioning

For a consistent versioning we decided to make use of `Semantic Versioning 2.0.0` http://semver.org. Its easy to understand, very common and known from many other software projects.


## Roadmap

- [x] Target stable release `1.0.0`
- [ ] 75 ~ 90% Test Coverage
- [x] Bug hunt and quality offensive
- [x] Travis implementation ([travis-ci.org](https://travis-ci.org/clickalicious/Doozr))
- [x] Scrutinizer-CI ([scrutinizer-ci.com](https://scrutinizer-ci.com/g/clickalicious/Doozr/))
- [x] Documentation ([doozr.readme.io](https://doozr.readme.io))
- [ ] Security check through 3rd-Party (Please get in contact with me)

[![Throughput Graph](https://graphs.waffle.io/clickalicious/Doozr/throughput.svg)](https://waffle.io/clickalicious/Doozr/metrics)


## Security Issues

If you encounter a (potential) security issue don't hesitate to get in contact with us `opensource@clickalicious.de` before releasing it to the public. So i get a chance to prepare and release an update before the issue is getting shared. Thank you!


## Participate & Share

... yeah. If you're a code monkey too - maybe we can build a force ;) If you would like to participate in either **Code**, **Comments**, **Documentation**, **Wiki**, **Bug-Reports**, **Unit-Tests**, **Bug-Fixes**, **Feedback** and/or **Critic** then please let us know as well!
<a href="https://twitter.com/intent/tweet?hashtags=&original_referer=http%3A%2F%2Fgithub.com%2F&text=Doozr%20-%20The%20lightweight%20PHP-Framework%20for%20high-performance%20projects%20%40phpfluesterer%20%23Doozr%20%23php%20https%3A%2F%2Fgithub.com%2Fclickalicious%2FDoozr&tw_p=tweetbutton" target="_blank">
  <img src="http://jpillora.com/github-twitter-button/img/tweet.png"></img>
</a>


## Sponsors

Thanks to our sponsors and supporters:

| JetBrains | Navicat |
|---|---|
| <a href="https://www.jetbrains.com/phpstorm/" title="PHP IDE :: JetBrains PhpStorm" target="_blank"><img src="https://resources.jetbrains.com/assets/media/open-graph/jetbrains_250x250.png" height="55"></img></a> | <a href="http://www.navicat.com/" title="Navicat GUI - DB GUI-Admin-Tool for MySQL, MariaDB, SQL Server, SQLite, Oracle & PostgreSQL" target="_blank"><img src="http://upload.wikimedia.org/wikipedia/en/9/90/PremiumSoft_Navicat_Premium_Logo.png" height="55" /></a>  |


###### Copyright
Icons made by <a href="http://www.flaticon.com/authors/sebastian-carl" title="Sebastian Carl">Sebastian Carl</a> licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a>


[1]: https://packagist "packagist.org - Package registry of composer"
[2]: http://www.php-fig.org/psr/psr-6/ "PSR-6 caching standard"
[3]: http://clickalicious.github.io/Doozr/ "Doozr's online code coverage report in HTML-format"
[4]: https://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html "Fast request routing using regular expressions"
[5]: https://github.com/FriendsOfPHP/uprofiler "Lightweight profiler for PHP (based on facebook/xhprof)"
[6]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[7]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[8]: http://www.php-fig.org/psr/psr-3/ "PSR-3: Logger Interface"
[9]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"
[10]: http://www.php-fig.org/psr/psr-7/ "PSR-7: HTTP message interfaces"
[11]: https://packagist.org/packages/relay/relay "A PSR-7 middleware dispatcher."
[12]: https://doozr.readme.io/ "The Doozr Developer Hub"
