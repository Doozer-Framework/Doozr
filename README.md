# DoozR
DoozR - The lightweight MVP PHP-Framework for high-performance websites


## Features
DoozR is designed for solving real world problems. It comes bundled with a lot of modules and a huge featureset. These modules and features enable you to build high-scalable applications with PHP. DoozR supports two different running modes - CLI and CGI - DoozR doesn't require you to write two complete different code-bases two run it on CLI and on CGI - DoozR will run your code no matter if CLI or CGI (except sessions). A nice routing mechanism with builtin I18n support for URLs empowers you to build internationalized applications on url level! Those feaures are only a few of so many more - try DoozR today ...


## Modules
 - Cache
 - Configreader
 - Crypt
 - Datetime
 - Filesystem
 - Form
 - I18n
 - Minify
 - Password
 - Session


## MVP
MVP is the only architecture which can be used by a web-framework per definition. DoozR implements MVP in the exact same meaning as you can read here on [Wikipedia: "Model–view–presenter"](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter). DoozR makes use of the observer-pattern to connect the View (observer) and Model (observer) within the Presenter (subject). View and Model (View must not exist - is optional) getting attached to Presenter so that the Presenter operates encapsulated from View and Model and connect both by triggering events on which they react (observer). It's that simple.


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
