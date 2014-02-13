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
