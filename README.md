# DoozR
*DoozR* - The lightweight **MVP** PHP-Framework for **high-performance** websites

<a href="https://twitter.com/intent/tweet?hashtags=&original_referer=http%3A%2F%2Fgithub.com%2F&text=Check+out+DoozR+-+The+lightweight+MVP+PHP-Framework+for+high-performance+websites+@phpfluesterer+%23clickalicious+%23DoozR+%23php&tw_p=tweetbutton&url=https%3A%2F%2Fgithub.com%2clickalicious%2DoozR" target="_blank">
  <img src="http://jpillora.com/github-twitter-button/img/tweet.png"></img>
</a>

## Features
*DoozR* is designed for solving real world problems. *DoozR* comes with a small but right set of preimplemented **services**. These services enable you to build high-scalable applications with PHP. DoozR supports building **CLI** and **CGI** applications. A powerful routing mechanism with builtin *I18n* support empowers you to build internationalized applications (on URL level!). But *DoozR* has so much more to offer ...


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
DoozR is build on plain PHP and provides a clean **MVP** structure for applications. The implementation is done using a *Supervising Controller* which was introduced by Martin Fowler - more details on that here: [Wikipedia: "Model–view–presenter"](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter). DoozR uses the observer-pattern to connect the View (observer) and Model (observer) through the Presenter (subject). View and Model (View and Model are both optional parts) getting attached to Presenter so that the Presenter operates encapsulated from View and Model and connect both by triggering events on which they react (observer). It's that simple.


## Install
See [Wiki: Your first steps with DoozR](https://github.com/clickalicious/DoozR/wiki/1.-Your-first-steps-with-DoozR) in the DoozR Wiki to get a first but detailed overview on "How DoozR works". If you can't wait to get DoozR - Clone it hot and tasty into a webroot of your choice:
```console
git clone git://github.com/clickalicious/DoozR.git .
```
and browse to it (e.g. [http://localhost/](http://localhost/)).


## More
For more detailed information on how we did things and how DoozR does things and so on visit the [Wiki](https://github.com/clickalicious/DoozR/wiki/_pages).


## Statistics
It's always difficult to guess how large a project is grown. But SLOC helps to get some interesting statistics out of a projects source-code:
```php
Total Physical Source Lines of Code (SLOC)                  = 74.005
Development Effort Estimate, Person-Years (Person-Months)   = 18,35 (220,26)
 (Basic COCOMO model, Person-Months = 2,4 * (KSLOC**1,05))
Schedule Estimate, Years (Months)                           = 1,62 (19,42)
 (Basic COCOMO model, Months = 2,5 * (person-months**0,38))
Estimated Average Number of Developers (Effort/Schedule)    = 11,34
Total Estimated Cost to Develop                             = € 1.906.742
 (average salary = € 43.290/year, overhead = 2,40)
```
