# I18n
## Abstract
The **I18n**-service provides a plain PHP *gettext™* like functionality implementation which is cappable of handling translation and localization of text and values. The **I18n**-Service is full compatible to the PHPTal-I18n-Interface and so it can be fully instrumentalized to work hand in hand together with *PHPTal*. The **I18n**-Service makes use of the Cache-Service and so it works with *Filesystem*, *memcached* and *Redis*. **I18n**-Service brings localization support with it. All common Internationalization (*I18n*) & Localization (*L10n*) tasks can be fulfilled by the I18n-Service.

### Features
- **Automatic locale detection** (Client/Browser detect)
- **Translation** (Through gettext or custom text interface)
- **Localization support** (Date/Time, Measure, Number, Currency)
- **Support for dynamic translations** (Parameter)
- ***gettext™* wrapper**
- **Cache support** (*memcached*, *Redis*, *Filesystem*)
- **PHPTal I18n Interface compatible** (works smooth together)

### Requirements
 - mbstring-Extension (PHP) [http://php.net/manual/de/mbstring.installation.php](http://php.net/manual/de/mbstring.installation.php "mbstring install php.net")
 - If using *gettext™* as Interface to translations the used locales (e.g. "de_DE" must be available on the serving system (OS) like statet here: 


### Modes
The DoozR I18n Service provides different interfaces to access/handle translations. Currently the following interfaces are supported:

 - Text (*ini*-File based)
 - Gettext (*gettext™*)

#### Caching
Caching of translations (e.g. if arguments passed to translation = dynamic translations) and in most cases static translation too are better handled from memory instead from files on disk. This will improve the performance of an application and scales a lot better. So the DoozR I18n Service provides some nice caching features and access to different caching backends like memcached, Redis, and some more (see **Supported caching backends**).

But not in all modes caching is supported! Gettext mode does not provide special caching mechanism (it uses runtime caching only). Instead it relies on gettext's very own caching built in. Double caching just increase calculation time. 

##### Overview Cache enabled interfaces

    Mode     |  Caching enabled?  |  Comment
    ------------------------------------------------------------------------
    Text     |         Y          |  Whole translation table gets cached
    Gettext  |         N          |  gettext™ provides builtin cache

##### Supported caching backends
DoozR I18n Service relies on DoozR Cache Service and so currently the following caching backends are supported:

 - *Filesystem*
 - *Memcached*
 - *Redis* (not public available - but soon :)

#### Setup 
The DoozR I18n Service requires you to setup a directory structure usable by the interface of your choice. The structure looks very similar at the end of the day but there are some slightly differences between *Gettext* and *Text* mode which we will cover here.

#### Gettext
If you choose *gettext™* as interface to translations then you need to create a directory structure like this:
 
    Path_To_Translationfiles\
        de_DE\                    (Locale[lowercase]_Countrycode[uppercase])
            Gettext\
                de_DE\            (Locale[lowercase]_Countrycode[uppercase])
                    LC_MESSAGES\
                        *.po      (Textdomain[namespace].po file(s))
 
The translation files are plain vanilla [.po files](http://de.wikipedia.org/wiki/GNU_gettext#.C3.9Cbersetzer ".po files on wikipedia"). You can edit those files with [Poedit](http://poedit.net/ "Poedit") for Windows for example.
 

#### Text
If you choose *Text* as interface to translations then you need to create a directory structure like this:
 
    Path_To_Translationfiles\
        de_DE\                    (Locale[lowercase]_Countrycode[uppercase])
            Text\
                de_DE\            (Locale[lowercase]_Countrycode[uppercase])
                    LC_MESSAGES\
                        *.ini     (Textdomain[namespace].po file(s))
 
Also no magic. The Text mode is based on ini-Files which can be handled fast and with good performance with native PHP and caching of contents is also not that difficult. We used this way a long time and currently evaluating to migrate over to gettext cause it is easier for translators to handle.

An example ini-Translation file would look like this:

    headline_1 = Hallo Welt!
    welcome_here = Das ist ein Demo-Text nur zur Demonstration einer simplen Übersetzung!
    x_books_in_my_y_shelves = Ich habe %1$s Bücher in meinen %2$s Regalen.
    This is "bar". = Das ist "bar".


### Shortcuts
- **Name**
`i18n`

- **Load**
`$i18n = DoozR_Loader_Serviceloader::load('i18n');`

### Components / Classes
 - **Service**
`DoozR_I18n_Service`

 - **Translator**
`DoozR_I18n_Service_Translator`

 - **Interface gettext**
`DoozR_I18n_Service_Interface_Gettext`

 - **Interface text**
`DoozR_I18n_Service_Interface_Text`

- **Detector**
`DoozR_I18n_Service_Detector`

- **Installer**
`DoozR_I18n_Service_Install`
Installs the gettext like shortcuts for the text interface:
> \_() \_\_() \_\_\_()


### Demonstration(s) / Example calls to Service
- Detect clients prefered locale:
`$locale = $i18n->getClientPreferedLocale();`

- Get all available locales (defined in config):
`$availableLocales = $i18n->getAvailableLocales();`

- Set available locales (override config):
`$i18n->setAvailableLocales(array('de', 'en'));`

- Get active locale:
`$activeLocale = $i18n->getActiveLocale();`

- Set active locale:
`$i18n->setActiveLocale('en');`

- Get encoding (e.g. UTF-8):
`$encoding = $i18n->getEncoding();`

- Set encoding:
`$i18n->setEncoding('ISO-8859-1');`

- Set encoding:
`$i18n->setEncoding('ISO-8859-1');`

- Get Translator for active locale:
`$i18n->getTranslator();`

- Get Translator for a conncrete locale:
`$i18n->getTranslator('en');`

### Installing locales on Linux
This was tested on Ubuntu 12.04.x LTS.
 
Check locales supported by server:	 

    > less /usr/share/i18n/SUPPORTED
	
You choose *ru_RU* as new locale for example and execute the following commands: 
	
	> sudo locale-gen ru_RU
	> sudo locale-gen ru_RU.UTF8
	> sudo dpkg-reconfigure locales

Don't forget to restart Webserver after adding new locale(s) cause gettext uses caching also for checking available locales!