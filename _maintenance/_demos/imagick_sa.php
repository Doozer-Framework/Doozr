<?php

require_once 'Controller/Core/Core.php';


$DoozR = DoozR_Core::getInstance();

$imagick = $DoozR->getModuleHandle('imagick');

$pathTemp = $DoozR->getPathTemp();


// set input file to process
$ok = $imagick->setTempPath($pathTemp);

if (!$ok) {
    // if there was an error then get it
    echo $imagick->getLastError() . '<br />';
    $imagick->reset();
}


$filesToProcess = array(
    $DoozR->getPathBase() . 'logo.png'
    //,$DoozR->getPathBase() . 'Data/checks2.png'
);


$targetFormat = 'png';


foreach ($filesToProcess as $fileToProcess) {

    $fileToProcessName  = basename($fileToProcess);
    $fileToProcessPath  = dirname($fileToProcess);
    $fileToProcessNoExt = substr($fileToProcessName, 0, strrpos($fileToProcessName, '.'));
    $fileToProcessExt   = substr($fileToProcessName, strrpos($fileToProcessName, '.')+1, strlen($fileToProcessName)-strrpos($fileToProcessName, '.')+1);


    // set input file to process
    $ok = $imagick->setInputFile($fileToProcess);

    if (!$ok) {
        // if there was an error then get it
        echo $imagick->getLastError() . '<br />';
        $imagick->reset();
    }


    // set input file to process
    $ok = $imagick->setOutputFile($DoozR->getPathBase() . 'data/' . $fileToProcessNoExt . '_d_l.' . $targetFormat, $targetFormat);

    if (!$ok) {
        // if there was an error then get it
        echo $imagick->getLastError() . '<br />';
        $imagick->reset();
    }


    // add converting commands / params
    $imagick->addCommand('matte');
    $imagick->addCommand('virtual-pixel', 'transparent');
    $imagick->addCommand('distort', 'Perspective "0,0,0,0  0,90,0,90  90,0,90,25  90,90,90,65"');


    // set input file to process (true = overwrite target if exists)
    $ok = $imagick->dispatch(true);

    if (!$ok) {
        // if there was an error then get it
        echo $imagick->getLastError() . '<br />';
    }

    // finally reset
    $imagick->reset();
}

?>
