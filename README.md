# DoozR
*DoozR* - The lightweight **MVP** PHP-Framework for **high-performance** websites

<a href="https://twitter.com/intent/tweet?hashtags=&original_referer=http%3A%2F%2Fgithub.com%2F&text=Check+out+DoozR+-+The+lightweight+MVP+PHP-Framework+for+high-performance+websites+@phpfluesterer+%23clickalicious+%23DoozR+%23php&tw_p=tweetbutton&url=https%3A%2F%2Fgithub.com%2clickalicious%2DoozR" target="_blank">
  <img src="http://jpillora.com/github-twitter-button/img/tweet.png"></img>
</a>

## Features
*DoozR* is designed for solving real world problems. *DoozR* comes with the right set of **services**. These services enable you to build high-scalable applications with PHP. *DoozR* supports **CLI** and **CGI** as well as running on PHP's internal webserver. A powerful routing mechanism with builtin *I18n* support empowers you to build internationalized applications (on url level!). But *DoozR* has so much more to offer ...


## Services
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


## MVP
DoozR is build with plain PHP and provides a clean **MVP** structure for applications. The implementation is done using a *Supervising Controller* which was introduced by Martin Fowler - more details on that here: [Wikipedia: "Model–view–presenter"](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter). DoozR uses the observer-pattern to connect the View (observer) and Model (observer) through the Presenter (subject). View and Model (View and Model are both optional parts) getting attached to Presenter so that the Presenter operates encapsulated from View and Model and connect both by triggering events on which they react (observer). It's that simple.


## API
*DoozR* provides a ready to use API - right after installation. The structure and routes are generic and built using best practices from PHP-community powered projects. If you plan to built a project providing an API to other developers or just to be able to build a SPA against a clean API by yourself then you should definitiv have a look at *DoozR*'s API structure or read about it here [Wiki: Build Apps powered by the built-in API](https://github.com/clickalicious/DoozR/wiki/X.-Build-Apps-powered-by-the-built-in-API).


## Install
See [Wiki: Your first steps with DoozR](https://github.com/clickalicious/DoozR/wiki/1.-Your-first-steps-with-DoozR) in the DoozR Wiki to get a first but detailed overview on "How DoozR works". If you can't wait to get DoozR - Clone it hot and tasty into a webroot of your choice:
```console
git clone git://github.com/clickalicious/DoozR.git .
```
and browse to it (e.g. [http://localhost/](http://localhost/)).


## More
For more detailed information on how we did things and how DoozR does things and so on visit the [Wiki](https://github.com/clickalicious/DoozR/wiki/_pages).
