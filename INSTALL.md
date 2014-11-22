# Install DoozR
This guide will show you how easy the installation of **DoozR** actually is. It's as easy as installing any other library via `composer`. The installer shipped with each copy of **DoozR** is cappable of doing all the nasty stuff you would otherwise be required to setup.


## Requirements
**DoozR** does not have heavy requirements. To get **DoozR** running you would just need *PHP*'s internal webserver. **DoozR** comes with a comfortable management tool for the internal webserver and a *mod_rewrite* replacement. So you can start developing with **DoozR** in seconds after install. You only need to run `php app/console --webserver=start` on `*nix` systems a `app/console --webserver=start` should be enough.  


### Notice
For production we would recommend a setup like this:

 - Webserver
 - mod_rewrite or something similar
 - PHP >= 5.3 

For real high-performance we **stronly** recommend the use of a memory based caching backend. So the requirements are:  

 - memcache (PHP-extension)
 - memcached (daemon)

We're currently working on a Redis integration to improve the performance if the tests show good results.


### Other environments
We are pretty sure you will get **DoozR** running in a good performance no matter which OS or distribution is used.


### Recommendation
We recommend you to use `Apache` in combination with `mod_rewrite`. But almost any other combination of a Webserver and a `mod_rewrite`-compatible rewrite engine will do its job too. We also recommend you to use *PHP* 5.3 at minimum. By doing so will increase the speed of **DoozR** and reduce the execution time. **DoozR** is capable of emulating required *PHP* 5.3 functionality in older versions (*PHP* <= 5.2) but due emulation the execution time will increase unneccesary. This isn't really a requirement but a recommendation. PHP provides many important features like **dev/urandom** or **get_all_headers()** (to name only two) only on linux platforms and as result windows based environments simply can't provide the same functionality as linux can. Again is **DoozR** capable of emulating - not all - but some important features PHP does not. But using this features in it's emulated version will result in a higher execution time than required. But feel free to check the emulated features in file [Framework/DoozR/Emulate/Php.php](https://github.com/clickalicious/DoozR/blob/master/Framework/DoozR/Emulate/Php.php) (PHP emulation) and in file [Framework/DoozR/Emulate/Linux.php](https://github.com/clickalicious/DoozR/blob/master/Framework/DoozR/Emulate/Linux.php) (Linux emulation). 


### High-Performance
`memcached` is not required but a strategic and important component in the high-performance web-framework structure of **DoozR**. **DoozR** relies internally on a bunch of interfaces to the `memcached` backend to prevent the core of **DoozR** from computing heavy operations twice (the parts currently covered were chosen by measurings/profiling-sessions with *XDebug* and/or *XHProf*). Results of heavy operations are stored in the `memchached` backend. And as result of this: As longer **DoozR** runs the faster it will serve responses to incoming requests! All modules shipped with **DoozR** relying on the high-performance *API* of **DoozR** and so they also profit from memcache support - as an extreme example the *I18n* module (DoozR_I18n_Module) with all the namespaces and translations are the perfect usecase for demonstrating how important a `memcached` backend can be. If `memcached` can't be used **DoozR** will fallback to filesystem caching and operates heavily on disk and the performance here depends then in I/O-speed of your servers *HDD*.


## Install
You can install **DoozR** via `composer`. We've implemented an installer based on Composer's CLI abstraction. So if you copy the `composer.json` content below you can decide if you want to bootstrap the recommended directory structure (*post-install-cmd*). You will be guided by our installer which copies all required files and folders to a directory of your choice. Just run `composer install`.
  
**composer.json**  
Navigate to a path where you want your environment running. Lets say you've planned to use **/var/wwwroot/doozr** as folder to start install. Create a file *composer.json* in exactly this folder. When *composer* is called it will create a folder **/var/wwwroot/doozr/web** which your vhost should point to later. Put the following content into your fresh created *composer.json*:

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

After this save and close the file. Open up a shell and navigate to the folder containing the fresh created *composer.json*. Execute this command:

    composer install

After composer has downloaded and installed all packages the DoozR installer will start. It guides you through the installation of a demo (bootstrap) project. This bootstrap project can help you getting started using DoozR.

<img src="http://i.imgur.com/gkyNcpn.jpg" />

## Environment
In our guides we always assume that your hostname ist *localhost* and that the webserver is serving at port *80*. The **DoozR** installation resides in the root (of a htdocs/wwwroot-folder) and so it's available in the browser by requesting the following URL: [http://localhost/Index/Index/](http://localhost/Index/Index/).

Now it's time to get **DoozR** serving it's first response for you. Make sure your vhost points to the */web* directory or start the internal webserver with our helper.

### Internal Webserver
After you have completed the installation you can run **DoozR** on PHP's internal webserver. All you need to do for this is to navigate to the install root and call `php app/console --webserver=start`. Default it serves on all devices (0.0.0.0) on port 80. To change the default behavior you can use the arguments `--port=8080` for example or `--interface=localhost` see `php app/console --help` for more.


## Your first request
After you set up a vhost or started the internal webserver you can point your browser to the URL [http://localhost/Index/Index](http://localhost/Index/Index). If everything works fine you will see a well known **Hello World** and **DoozR**'s debug bar of course.
