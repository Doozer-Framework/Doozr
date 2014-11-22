**DoozR** - A lightweight but powerful *MVP* PHP-Framework for **high-performance** websites. Designed to deliver content of high-performance websites fast and rock solid. Designed from developers for developers. **DoozR** comes with the right set of **services**. These services enable you to build **high-scalable** applications with *PHP*. **DoozR** supports **CLI** and **CGI** as well as running on *PHP*'s internal webserver. A powerful routing mechanism with builtin *I18n* support empowers you to build internationalized applications. But **DoozR** has so much more to offer.


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
    

After this save and close the file. Open up a shell and navigate to the folder containing the fresh created `composer.json`. Execute this command:

    composer install

After composer has downloaded and installed all packages the **DoozR** installer will start. It guides you through the installation of a demo (bootstrap) project. This bootstrap project can help you getting started using **DoozR**.

<img src="http://i.imgur.com/gkyNcpn.jpg" />

## Runs on PHP's internal webserver
After you have completed the installation you can run **DoozR** on *PHP*'s internal webserver. All you need to do for this is to navigate to the install root and call `php app/console --webserver=start`. Default it serves on all devices (0.0.0.0) on port 80. To change the default behavior you can use the arguments `--port=8080` for example or `--interface=localhost` see `php app/console --help` for more.


## Services Built-In
**DoozR** is shipped with the right set of internal services ...
 
 - **Acl**
 - **Cache**
 - **Compact**
 - **Config**
 - **Crypt**
 - **Datetime**
 - **Filesystem**
 - **Form**
 - **Http**
 - **I18n**
 - **OAuth2**
 - **Password**
 - **Rest**
 - **Session**
 - **Template**
 - **Virtualfilesystem**

... and works smooth in combination with additional `composer` packages.

## MVP
**DoozR** is build with plain PHP and provides a clean **MVP** structure for applications. The implementation is done using a *Supervising Controller* which was introduced by Martin Fowler - more details on that here: [Wikipedia: "Model–view–presenter"](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter). **DoozR** uses the observer-pattern to connect the View (observer) and Model (observer) through the Presenter (subject). View and Model (View and Model are both optional parts) getting attached to Presenter so that the Presenter operates encapsulated from View and Model and connect both by triggering events on which they react (observer). It's that simple.

![Model_View_Presenter_GUI_Design_Pattern](https://upload.wikimedia.org/wikipedia/commons/d/dc/Model_View_Presenter_GUI_Design_Pattern.png)

## API
**DoozR** provides a ready to use *API* - right after installation. The structure and routes are generic and built using best practices from PHP-community powered projects. If you plan to built a project providing an *API* to other developers or just to be able to build a *SPA* against a clean *API* by yourself then you should definitiv have a look at **DoozR**'s *API* structure.

<a href="https://twitter.com/intent/tweet?hashtags=&original_referer=http%3A%2F%2Fgithub.com%2F&text=DoozR%20-%20The%20lightweight%20PHP-Framework%20for%20high-performance%20projects%20%40phpfluesterer%20%23DoozR%20%23php%20https%3A%2F%2Fgithub.com%2Fclickalicious%2FDoozR&tw_p=tweetbutton" target="_blank">
  <img src="http://jpillora.com/github-twitter-button/img/tweet.png"></img>
</a>
