<?php

// include DoozR bootstrapper
require_once '../Framework/Core/DoozR.bootstrap.php';

// instanciate DoozR
$DoozR = DoozR_Core::getInstance();

pred(DoozR_Config_Ini::getInstance(DOOZR_DOCUMENT_ROOT.'Data\\Private\\Config\\Config.ini.php')->get('SMARTY.DIR_TEMPLATES_C'));

die('???');



require_once '../Framework/Core/Controller/DoozR.extend.php';
require_once '../Framework/Core/Controller/Config/Manager/AConfigManager.class.php';
require_once '../Framework/Core/Controller/Config/Manager/IConfigManager.class.php';
require_once '../Framework/Core/Controller/Config/Manager/Ini/ConfigManagerIni.class.php';
require_once '../Framework/Module/DoozR/Cache/Module.php';

$pathConfig = 'C:\\Programme\\xampp\\htdocs\\DoozR\\Framework\\Data\\Private\\Config\\Config.ini.php';

pre('vorher');

$start = microtime();
$configManager = DoozR_Config_Ini::getInstance($pathConfig);
//$configManager->save('C:\\meinkrampf.ini');

$end = microtime();

pre(getMicrotimeDiff($start, $end));



function getMicrotimeDiff($microtimeStart = false, $microtimeEnd = false)
{
    $microtimeEnd = (!$microtimeEnd) ? microtime() : $microtimeEnd;
    list($microtimeStart_dec, $microtimeStart_sec) = explode(' ', $microtimeStart);
    list($microtimeEnd_dec, $microtimeEnd_sec) = explode(' ', $microtimeEnd);
    return sprintf("%0.12f", ($microtimeEnd_sec - $microtimeStart_sec + $microtimeEnd_dec - $microtimeStart_dec));
}


//pred($parsedConfig);



?>
