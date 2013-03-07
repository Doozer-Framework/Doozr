<?php

/**
 * cause of compatibility issues (SVN external) we need to give a possiblity to 
 * setup/configure the path to app-folder!
 * 
 * this is an example - in theory we assume that 'app' is a folder on the same level as 'core' and 'data'
 * but if the path differs (e.g. if you use an external (SVN) reference to the folder 'framework') and so 
 * the folder 'app' is outside the default folder so you need a possibility to define that ...
 */
$s = DIRECTORY_SEPARATOR;
define('DOOZR_APP_PATH', str_replace('Include'.$s.'pathfinder.include.php', '', __FILE__));
define('DOOZR_APP_CONFIG_PATH', DOOZR_APP_PATH.'Data'.$s.'Private'.$s.'Config'.$s);

?>
