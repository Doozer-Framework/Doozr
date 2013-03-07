to save you some time with translating and prevent copying language 
files all the time, you can use a redirect to another locale folder to 
use it's translation files instead. just place a file "redirect" into 
the concerning locale subdirectory which contains the name of the folder 
with the translation files to use. in this example you wouldn't want to 
translate everything for the UK again, if you have already done so for 
the normal "en" folder. so i've put a redirect file into this folder 
which contains the string "en" to tell the class to use that locale 
instead.
