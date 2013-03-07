<?php
/*
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 *   must display the following acknowledgement: This product includes software
 *   developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 */

// include DoozR core
require_once 'Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance(null, null);


$pathTemp = $DoozR->getPathTmp();
$log_dir = $DoozR->getPathLog();



// get new instance of imagick class
$imagick = $DoozR->getModuleHandle('imagick');


// get image-magick convert info
//$info = $imagick->getImageMagickInfo();
//print_r($info);

// set input file to process
$ok = $imagick->setTempPath($pathTemp);

if (!$ok) {
    // if there was an error then get it
    echo $imagick->getLastError() . '<br />';
    $imagick->reset();
}


$filesToProcess = array(
    $DoozR->getPathBase() . 'Data/checks.png'
);


$targetFormat = 'png';


//foreach ($filesToProcess as $fileToProcess) {
    $fileToProcess = $filesToProcess[0];


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

    // what should be applied on input file
    // add converting commands / params
    $imagick->addCommand('matte');
    $imagick->addCommand('virtual-pixel', 'transparent');
    $imagick->addCommand('distort', 'Perspective "0,0,0,0  0,90,0,90  90,0,90,25  90,90,90,65"');


    // set input file to process
    $ok = $imagick->setOutputFile($DoozR->getPathBase() . 'data/' . $fileToProcessNoExt . '_d_l.' . $targetFormat, $targetFormat);

    if (!$ok) {
        // if there was an error then get it
        echo $imagick->getLastError() . '<br />';
        $imagick->reset();
    }

    // set input file to process (true = overwrite target if exists)
    $ok = $imagick->dispatch(true, true);

    if (!$ok) {
        // if there was an error then get it
        echo $imagick->getLastError() . '<br />';
    }

    // finally reset
    $imagick->reset();
//}
?>