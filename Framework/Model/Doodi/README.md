# *Doodi*
## What is *Doodi* and what does it stand for
*Doodi* is an acronym for **D**oozR's-**O**bject-**O**riented-**D**atabase-**I**nterface. *Doodi* ist cappable of masking (Proxy-/Observer-Pattern) almost any Database **OxM** out there.

## *Doodi's* main features
 * Generic proxying of almost any OxM (use any of the existing OxM's with a name and namespace matching you projects requirements)
 * Static pregenerated routes (for good speed)
 * Adds oberserver-pattern to database-layer (to attach loggers or secondary database libraries ...)
 * SP1 compatible and full featured autoloading of any library

## Howto create proxy-classes, route and how to use

Let's say that we want to implement the new OxM called **Foo** - We ...

... put the files of **Foo** to the folder */Framework/Model/Lib/Foo/*
... change the config (.config) as you can see here:

        "database": {
            "enabled": true,
            "errormessage": "Error ...",
            "proxy": "Foo",
	        "oxm": "Foo",
	        "bootstrap": "bootstrap.php",
	        "route": "Route.php",
	        "docroot": "{DOOZR_DOCUMENT_ROOT}Model/Doodi/Foo/",
	        "host": "localhost",
	        "port": "1234",
	        "database": "doozr",
	        "user": "",
	        "password": ""
        }


**enabled**      [boolean] (true|false)
**errormessage** [string]  (e.g. "Sorry but something went ...")
**proxy**        [string]  (e.g. "Foo" or "Couchdb")
**oxm**          [string]  (name of the proxied oxm e.g. "phpillow")
**bootstrap**    [string]  (a file to execute as custom oxm's bootstrapper)
**route**        [string]  (the name of the file containing the route e.g. "Route.php")
**docroot**      [string]  (path to the proxy)
**host**         [string]  (the host of the database e.g. "localhost")
**port**         [string]  (the port the database is listening e.g. "1234")
**database**     [string]  (the database to connect to e.g. "Test")
**user**         [string]  (the username used for connection)
**password**     [string]  (the password used for connection)

... run the script *cli.php* like in the following example:

    php cli.php /Demo/Screen /proxy Foo /library phpillow /bootstrap bootstrap.php /pathlibrary Model\Lib\ /pathdoodi Model\Doodi\ /exclude test /pattern SPLIT_BY_CAMELCASE

The script will write the result to the outputfolder passed as named argument *pathdoodi*/Foo/.
After this step everything should be in place and ready to use. You will now (hopefully) see a folderstructure with proxy-classes in it's final position *Framework/Model/Doodi/Foo/*

### Folder structure example "Foo"

    Framework\Model\Lib\
                        foo\
                        phpillow\
                        php-object-freezer\
                                             (contains all libraries)
                    Doodi\
                          Foo\      -> proxy to foo
                          Couchdb\  -> proxy to phpillow
                          Freezer\  -> proxy to php-object-freezer
                                       (all generated proxy classes *)
                                  README.md  [this file you currently reading]

*Whenever you see "Foo" above its the name we defined for masking "Foo".
Just for make phpillow easy replacable without touching any point of the code
elsewhere. The plan is that we exchange phpillow with a native C/C++ extension
to speed the process up. So we only need to dispatch to native extension
instead to phpillow and all we have to do for that is changing a few calls in
proxies (only one way for example).*

###Configuration
"Foo" would also be the name of the config setting in ".config" in DoozR's core config and in App-config configurable (override works):

        "database": {
            "enabled": true,
            "errormessage": "Error ...",
            "lib": "Foo",
	        "oxm": "Foo",
	        "bootstrap": "bootstrap.php",
	        "route": "Route.php",
	        "docroot": "{DOOZR_DOCUMENT_ROOT}Model/Doodi/Foo/",
	        "host": "localhost",
	        "port": "1234",
	        "database": "doozr",
	        "user": "",
	        "password": ""
        }

**enabled**      [boolean] (true|false)
**errormessage** [string]  (e.g. "Sorry but something went ...")
**proxy**        [string]  (e.g. "Foo" or "Couchdb")
**oxm**          [string]  (name of the proxied oxm e.g. "phpillow")
**bootstrap**    [string]  (a file to execute as custom oxm's bootstrapper)
**route**        [string]  (the name of the file containing the route e.g. "Route.php")
**docroot**      [string]  (path to the proxy)
**host**         [string]  (the host of the database e.g. "localhost")
**port**         [string]  (the port the database is listening e.g. "1234")
**database**     [string]  (the database to connect to e.g. "Test")
**user**         [string]  (the username used for connection)
**password**     [string]  (the password used for connection)

###How to use
Using Doodi to access all the different Libs makes everything a bit easier.

    require_once '../Framework/DoozR/Bootstrap.php';

    $DoozR = DoozR_Core::getInstance();
    $registry = DoozR_Registry::getInstance();
    $config = $registry->config;
    $model = $registry->model;

    $connection = $model->connect(
        $config->database->host,
        $config->database->port,
        $config->database->user,
        $config->database->password
    );

    $databaseHandle = $model->open($config->database->database);

    $model->close();

    $model->disconnect();
