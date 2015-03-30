<img src="https://avatars2.githubusercontent.com/u/514566?v=3&u=4615dfc4970d93dea5d3eaf996b7903ee6e24e20&s=140" align="right" />
---
![Logo of DoozR](logo-large.png)
The **lightweight** PHP-Framework for **high-performance** websites

This guide will show you how the steps required to install `DoozR`. It's as easy as installing a library via `composer`. The installer shipped with each copy of `DoozR` is cappable of doing all the nasty stuff you would otherwise be required to setup manually like symlinks for assets and so on.


## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [A first request](#a-first-request)
- [Architecture »](../docs/ARCHITECTURE.md)


## Requirements

To get `DoozR` running you would just need PHP's internal webserver. This is good to start developing easy but of course not recommended for productive environments. `DoozR` comes with a management tool for the internal webserver and a smart `mod_rewrite` replacement. So you can start developing with `DoozR` in seconds after install. You only need to run `php app/console --webserver=start` (on `*nix` systems a `app/console --webserver=start` should be enough).  

`DoozR` requires 

 - PHP >= 5.3 

it can emulate all required features from PHP 5.4 and up to 5.6. So don't panic if you run an older version of PHP. But please have a look on the Notice section as well. We don't recommend to use a PHP-Version < 5.5 cause of performance gains possible when switching to a newer version! 


### Notice!

For production environments we recommend a setup at minimum like this:

 - Real webserver like Apache (preferred) or Nginx
 - mod_rewrite or something similar (Nginx alternative)
 - PHP >= 5.5

For real high-performance we **strongly!** recommend the use of a memory based caching backend. `DoozR` brings native support for `Memcached` as caching backend so `DoozR` does not require any extension to access `Memcached`. 


### Other environments

We are pretty sure you will get `DoozR` running with a good performance no matter which OS or distribution is used. I develop on a `Windows` based system and can run the whole stack without any problems :) 


### Recommendation

We recommend you to use `Apache` in combination with `mod_rewrite`. But almost any other combination of a Webserver and a `mod_rewrite`-compatible rewrite engine will do its job too. We also recommend you to use PHP 5.5 at minimum. By doing so will increase the speed of `DoozR` and reduce the execution time. `DoozR` is capable of emulating required PHP > 5.3 functionality in older versions but due emulation the execution time will increase unneccesary. This isn't really a requirement but a recommendation. PHP provides many important features like `/dev/urandom` or `get_all_headers()` (to name only two) only on linux platforms and as result windows based environments simply can't provide the same functionality as linux can. Again is `DoozR` capable of emulating - not all - but some important features PHP does not provide on all platforms. But using this features in it's emulated version will result in a higher execution time than required. But feel free to check the emulated features in file [lib/DoozR/Emulate/Php.php](https://github.com/clickalicious/DoozR/blob/master/lib/DoozR/Emulate/Php.php) (PHP emulation) and in file [lib/DoozR/Emulate/Linux.php](https://github.com/clickalicious/DoozR/blob/master/lib/DoozR/Emulate/Linux.php) (Linux emulation). 


### High-Performance

`Memcached` is not required but a strategic and important component in the high-performance web-framework structure of `DoozR`. `DoozR` relies internally on a bunch of interfaces to the `memcached` backend to prevent the core of `DoozR` from computing heavy operations twice (the parts currently covered were chosen by measurings/profiling-sessions with *XDebug* and/or *XHProf*). Results of heavy operations are stored in the `memchached` backend. And as result of this: As longer `DoozR` runs the faster it will serve responses to incoming requests! All modules shipped with `DoozR` relying on the high-performance *API* of `DoozR` and so they also profit from memcache support - as an extreme example the *I18n* module (DoozR_I18n_Module) with all the namespaces and translations are the perfect usecase for demonstrating how important a `memcached` backend can be. If `memcached` can't be used `DoozR` will fallback to filesystem caching and operates on disk and the performance here depends then in I/O-speed of your servers *HDD*.


## Installation

You can install `DoozR` easy and quick via `composer`. Just insert the following JSON-structure in a file named  `composer.json`:

```javascript
{
  "name": "company/package",
  "minimum-stability": "dev",
  "require": {},
  "require-dev": {
    "clickalicious/doozr": "dev-master"
  },
  "scripts": {
    "post-install-cmd": [
      "DoozR_Installer_Framework::postInstall"
      ]
  }
}
```

and put in i a location where you want to install your project to. If you're about to use `/var/www/myproject` as root directory then create the file `composer.json` in exactly this folder. When `composer` is executed it will parse the `composer.json`, install the dependencies and executes the post install command (post-install-cmd). This command creates a directory `/var/www/myproject/web` which your vhost should point to later. To start the installation just run the following command

    composer install

You will be guided by an installer which copies all required files and folders to a directory of your choice for example. The installer also creates some symlinks for assets. 

<img src="http://i.imgur.com/gkyNcpn.jpg" />

### Environment

In our guides we always assume that your hostname ist *localhost* and that the webserver is serving at port *80*. The `DoozR` installation resides in the root (of a htdocs/wwwroot-folder) and so it's available in the browser by requesting the following URL: [http://localhost/Index/Index/](http://localhost/Index/Index/).

Now it's time to get `DoozR` serving it's first response for you. Make sure your vhost points to the `/web` directory or start the 
internal webserver with our helper.

### Runs on PHP's internal webserver

After you have completed the installation you can run `DoozR` on PHP's internal webserver. All you need to do for this is to navigate to the install root and call `php app/console --webserver=start`. Default it serves on all devices (0.0.0.0) on port 80. To change the default behavior you can use the arguments `--port=8080` for example or `--interface=localhost` see `php app/console --help` for more.

Examples 

    Start Webserver on interface localhost only (Port 80 default):
    php app/console --webserver=start --interface=localhost

    Start Webserver on port 8080 instead of default 80:
    php app/console --webserver=start --port=8080

## A first request

After you set up a vhost or started the internal webserver you can point your browser to the URL [http://localhost/Index/Index](http://localhost/Index/Index). If everything works fine you will see a simple and well known **Hello World** and `DoozR`'s debug bar of course.
