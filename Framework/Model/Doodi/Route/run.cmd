@echo off
cls

REM %1 = Name of proxy 'Freezer'
REM %2 = Path to lib 'Model/Lib/php-object-freezer/'
REM %3 = Path to Doodi 'Model/Doodi/'
REM %4 = Namespace separator '/'
REM %5 = Translate to '_' 

php cli.php %1 %2 %3 %4 %5
