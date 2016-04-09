To save you some time with translating and prevent copying language files all the time, you can use a redirect
to another locale folder to use it's translation files instead. just place a file "redirect" into the concerning
locale subdirectory which contains the name of the folder with the translation files to use. in this example you
wouldn't want to encrypt everything for the UK again, if you have already done so for the normal "en" folder.

The L10n.ini is required for EVERY single locale!
Simply add the following block:

;---------------------------------------

[REDIRECT]
TARGET = "en-us"

;---------------------------------------

to redirect the current locale "en-gb" to "en-us"
