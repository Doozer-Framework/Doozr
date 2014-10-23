# DoozR
*DoozR* - The lightweight but powerful **MVP** PHP-Framework for **high-performance** websites. Designed to deliver content of high performance websites fast and solid. Designed from Developers for Developers. *DoozR* comes with the right set of **services**. These services enable you to build high-scalable applications with PHP. *DoozR* supports **CLI** and **CGI** as well as running on PHP's internal webserver. A powerful routing mechanism with builtin *I18n* support empowers you to build internationalized applications (on url level!). But *DoozR* has so much more to offer.

<a href="https://twitter.com/intent/tweet?hashtags=&original_referer=http%3A%2F%2Fgithub.com%2F&text=Check+out+DoozR+-+The+lightweight+MVP+PHP-Framework+for+high-performance+websites+@phpfluesterer+%23clickalicious+%23DoozR+%23php&tw_p=tweetbutton&url=https%3A%2F%2Fgithub.com%2clickalicious%2DoozR" target="_blank">
  <img src="http://jpillora.com/github-twitter-button/img/tweet.png"></img>
</a>


## Install (Composer)
You can install *DoozR* easily via *composer*!
  
**composer.json**  
Navigate to a path where you want your environment running. If you plan to use **/var/wwwroot/doozr** as folder to start install then create a file *composer.json* in exactly this folder. When *composer* is called it will create a folder **/var/wwwroot/doozr/web** which your vhost should point to later. Put the following content into your fresh created *composer.json*:

    {
        "name": "company/package",
        "minimum-stability": "dev",
        "require": {},
        "require-dev": {
            "clickalicious/doozr": "dev-master"
        }
    }
    

After this save and close the file. Open up a shell and navigate to the folder containing the fresh created *composer.json*. Execute this command:

    composer install


This is currently the only "official" provided way installing *DoozR*. *DoozR* of course supports being cloned via *git* or being symlinked from another (shared) folder. Good luck :)

See [Wiki: Your first steps with DoozR](https://github.com/clickalicious/DoozR/wiki/1.-Your-first-steps-with-DoozR) in the DoozR Wiki to get a first but detailed overview on "How DoozR works".


## Built-In Services
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


## MVP
DoozR is build with plain PHP and provides a clean **MVP** structure for applications. The implementation is done using a *Supervising Controller* which was introduced by Martin Fowler - more details on that here: [Wikipedia: "Model–view–presenter"](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter). DoozR uses the observer-pattern to connect the View (observer) and Model (observer) through the Presenter (subject). View and Model (View and Model are both optional parts) getting attached to Presenter so that the Presenter operates encapsulated from View and Model and connect both by triggering events on which they react (observer). It's that simple.


## API
*DoozR* provides a ready to use API - right after installation. The structure and routes are generic and built using best practices from PHP-community powered projects. If you plan to built a project providing an API to other developers or just to be able to build a SPA against a clean API by yourself then you should definitiv have a look at *DoozR*'s API structure or read about it here [Wiki: Build Apps powered by the built-in API](https://github.com/clickalicious/DoozR/wiki/X.-Build-Apps-powered-by-the-built-in-API).



## More
For more detailed information on how we did things and how DoozR does things and so on visit the [Wiki](https://github.com/clickalicious/DoozR/wiki/_pages).


<a href="https://twitter.com/intent/tweet?hashtags=&original_referer=http%3A%2F%2Fgithub.com%2F&text=Check+out+DoozR+-+The+lightweight+MVP+PHP-Framework+for+high-performance+websites+@phpfluesterer+%23clickalicious+%23DoozR+%23php&tw_p=tweetbutton&url=https%3A%2F%2Fgithub.com%2clickalicious%2DoozR" target="_blank">
  <img src="http://jpillora.com/github-twitter-button/img/tweet.png"></img>
</a>