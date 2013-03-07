; <?php die();  // DO NOT REMOVE THIS LINE ?>
; 2003-2006 © Michael Wimmer (flaimo@gmx.net | http://flaimo.com)
; -------------------------------------------------------------------------
; Default settings for i18n
;
; This ini file + all classes should be placed OUTSIDE the www directory!

[Locale]
; this locale must always exist!
default_locale = "en"
default_language = "en"
default_country = "us"
; path to the folder where the locale directories are
locales_path = "{DOOZR_APP_ROOT}/Data/Private/Locale/"

[Translator]
; modus = Text | Gettext | MySQL | SQLite | SQLite3 | XML
mode = "Text"
; comma seperated string with namespaces/translation files which should be loaded with every pagecall
default_namespaces = "lang_main"
; triggers E_USER_WARNING errors if strings couldn't be translated or namespaces couldn't be found
show_errormessages = TRUE
; en- or disable locale checking (for ex. everytime a user with a cookie set revisits the page); FALSE = faster
locale_checking = TRUE
; set this to false if you don't use any alias languages. FALSE = faster
use_alias_locales = TRUE

; further settings are located in every TranslatorXXX class depending on the method used

[Cache]
cache_dir = "{DOOZR_DOCUMENT_ROOT}Data\Private\Cache\Localization"
; cache time in sec
cache_time = 86400
file_extention = "txt"
file_prefix = ""
check_cache_dir = TRUE

[FormatDate]
; 0 = standard format, 1 = iso date, 2 = swatch date
default_timeset = 0

[FormatNumber]
default_minor_unit = 2
default_decimal_point = "."
default_thousands_sep = ""

[FormatString]
; highlight abbr, dfn and acronyms
default_specialwordsstatus = 1
replace_char = "*"

[Measure]
default_input_system = "si"