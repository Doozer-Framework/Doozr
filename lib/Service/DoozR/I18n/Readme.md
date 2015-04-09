# I18n
## Overview
The **I18n** Service provides a real good and lightweight interface to *gettext™* as well as a very *gettext™* like functionality + caching provided in plain vanilla PHP - often interesting for environments where you are not able to setup *gettext™*. The **I18n** Service is cappable of handling translation and localization of text and values. The **I18n** Service is also full compatible with the *PHPTal* I18n interface and so it works hand in hand together with *PHPTal*. The **I18n** Service makes use of the **Cache** Service and provides access to the following caching backends: *filesystem*, *memcached* and *Redis* (comming soon). All common Internationalization (*I18n*) & Localization (*L10n*) tasks can be easily fulfilled with the **I18n** Service.

### Features
- **Automatic locale detection** (client/browser detect)
- **Translation** (through *gettext™* or custom *text* interface)
- **Localization support** (date/time, measure, number, currency)
- **Support for dynamic translations** (parameter/arguments)
- ***gettext™* wrapper**
- **Cache support** (*memcached*, *Redis*, *filesystem*)
- **PHPTal I18n Interface compatible** (works smooth together)

### Modes
The **I18n** Service provides different interfaces to handle translations. Currently the following interfaces are supported:

 - **Text** (*ini*-File based)
 - **Gettext** (*gettext™*)

### Caching
Translating from memory instead from files on disk is much faster so the **I18n** Service makes intensive use of caching to increase the performance of an application. This also helps to scale better. The DoozR I18n Service provides access to different caching backends like memcached, Redis, and some more (see **Supported caching backends**).

**Important!** Caching is not available in *gettext™* runtimeEnvironment. If the runtimeEnvironment is *gettext™* the **I18n** Service does not provide any caching (beside runtime caching) instead it relies on gettext's very own caching built in. Double caching would just increase calculation time. To get an overview of the cache enabled interfaces (modes) see the following table ...

##### Cache enabled interfaces

    Mode     |  Caching enabled?  |  Comment
    ------------------------------------------------------------------------
    Text     |         Y          |  Whole translation table gets cached
    Gettext  |         N          |  gettext™ provides builtin cache

##### Supported caching backends
The **I18n** Service relies on the **Cache** Service and so the following caching backends are supported ...

 - **Filesystem** (cache on HDD)
 - **Memcached** (cache in Memory)
 - **Redis** (cache in Memory - not yet public available)

#### Setup 
The **I18n** Service requires you to setup a directory structure usable by the interface of your choice. The structure looks very similar at the end of the day but there are some slightly differences between *Gettext* and *Text* runtimeEnvironment which we will cover next.

#### Gettext
If you choose *gettext™* as interface to translations then you need to create a directory structure like this:
 
    Path_To_Translationfiles\
        de-de\                    (Locale[lowercase]_Countrycode[uppercase])
            Gettext\
                de_DE\            (Locale[lowercase]_Countrycode[uppercase])
                    LC_MESSAGES\
                        *.po      (Textdomain[namespace].po file(s))
 
The translation files are plain vanilla [.po files](http://de.wikipedia.org/wiki/GNU_gettext#.C3.9Cbersetzer ".po files on wikipedia"). You can edit those files with [Poedit](http://poedit.net/ "Poedit") for Windows for example or any program of your choice for your favorite OS.
 

#### Text
If you choose *Text* as interface to translations then you need to create a directory structure like this:
 
    Path_To_Translationfiles\
        de-de\                    (Locale[lowercase]_Countrycode[uppercase])
            Text\
                de_DE\            (Locale[lowercase]_Countrycode[uppercase])
                    LC_MESSAGES\
                        *.ini     (Textdomain[namespace].po file(s))
 
Also no magic. The Text runtimeEnvironment is based on ini-Files which can be handled fast and with good performance with native PHP and caching of contents is also not that difficult. We used this way a long time and currently evaluating to migrate over to gettext cause it is easier for translators to handle.

An example ini-Translation file would look like this:

    headline_1 = Hallo Welt!
    welcome_here = Das ist ein Demo-Text nur zur Demonstration einer simplen Übersetzung!
    x_books_in_my_y_shelves = Ich habe %1$s Bücher in meinen %2$s Regalen.
    This is "bar". = Das ist "bar".
You can edit these files with any text-editor you like. Just place one `key = value` pair on a  line and thats it.

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

### Examples (code)
Detect clients prefered locale:

    $locale = $i18n->getClientPreferredLocale();

Get all available locales (defined in config):

    $availableLocales = $i18n->getAvailableLocales();

Set available locales (override config):

    $i18n->setAvailableLocales(array('de', 'en'));

Get active locale:

    $activeLocale = $i18n->getActiveLocale();

Set active locale:

    $i18n->setActiveLocale('en');

Get encoding (e.g. UTF-8):

    $encoding = $i18n->getEncoding();

Set encoding:

    $i18n->setEncoding('ISO-8859-1');

Set encoding:

    $i18n->setEncoding('ISO-8859-1');

Get Translator for active locale:

    $i18n->getTranslator();

Get Translator for a conncrete locale:

    $i18n->getTranslator('en');


## Install
How to get the service running.

### Requirements
 - *mbstring*-Extension (*PHP*) [http://php.net/manual/de/mbstring.installation.php](http://php.net/manual/de/mbstring.installation.php "mbstring install php.net")
 - If using *gettext™* as Interface to translations the used locales (e.g. "de_DE" must be available on the serving system (OS).


#### Installing locales on Linux
This was tested on Ubuntu 12.04.x LTS.
 
Check locales supported by server:	 

    > less /usr/share/i18n/SUPPORTED
	
You choose *ru_RU* as new locale for example and execute the following commands: 
	
	> sudo locale-gen ru_RU
	> sudo locale-gen ru_RU.UTF8
	> sudo dpkg-reconfigure locales

Don't forget to restart Webserver after adding new locale(s) cause gettext uses caching also for checking available locales!
