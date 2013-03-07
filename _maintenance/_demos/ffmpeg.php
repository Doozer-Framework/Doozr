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
$DoozR = DoozR_Core::getInstance('');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DoozR - welcome</title>
</head>
<body>
<pre>

<?php

$outputDirectory = $DoozR->getPathBase();
$log_dir = $DoozR->getPathLog();

// get new instance of ffmpeg class
$videotools = $DoozR->getModuleHandle('videotools');

// debug true - on error die(msg)
$videotools->setDebug(true);

// input movie files
$filesToProcess = array(
    $DoozR->getPathBase() . 'Data/test.wmv'
);


$outputWidth  = 640;
$outputHeight = 368;
$mode         = 'keep';


// loop through the files to process
foreach($filesToProcess as $key => $file) {

    // get the filename parts
    $filenameExtension = basename($file);
    $filenameNoExt = substr($filenameExtension, 0, strrpos($filenameExtension, '.'));

    // set the input file
    $ok = $videotools->setInputFile($file);

    // check the return value in-case of error
    if (!$ok) {
        // if there was an error then get it
        echo $videotools->getLastError() . '<br />';
        $videotools->reset();
        continue;
    }

    // set the output dimensions
    $ok = $videotools->setVideoOutputDimensions($outputWidth, $outputHeight, $mode);

    // check the return value in-case of error
    if (!$ok) {
        // if there was an error then get it
        echo $videotools->getLastError() . '<br />';
        $videotools->reset();
        continue;
    }

    // general 2 pass
    $videotools->set2Pass(true);

    // convert using 2-passes and generate FLV-Fallback file
    $twopass     = true;
    $fallbackFLV = true;

    // set the output dimensions
    $ok = $videotools->setFormatToH264($twopass, $fallbackFLV);

    // check the return value in-case of error
    if (!$ok) {
        // if there was an error then get it
        echo $videotools->getLastError() . '<br />';
        $videotools->reset();
        continue;
    }

    // set the output details and overwrite if nessecary
    $ok = $videotools->setOutput($outputDirectory, $filenameNoExt . '.mp4', Videotools::OVERWRITE_EXISTING);

    // check the return value in-case of error
    if (!$ok) {
        // if there was an error then get it
        echo $videotools->getLastError() . '<br />';
        $videotools->reset();
        continue;
    }

    // execute the ffmpeg command and log the calls and ffmpeg results (true)
    $result = $videotools->execute();


    // get the last command given
    $command = $videotools->getLastCommand();

    // check the return value in-case of error
    if ($result === false) {
        echo $videotools->getLastError() . '<br />';
        $videotools->reset();
        continue;
    } else if ($result === Videotools::RESULT_OK_BUT_UNWRITABLE) {
        // ok but a manual move is required. The file to move can be it can be retrieved by $toolkit->getLastOutput();
        echo 'Result OK but File move failed!<br />';
        $videotools->reset();
        continue;
    } else if ($result === Videotools::RESULT_OK) {
        // everything is ok.
    }

    // reset
    $videotools->reset();
}

?>
</pre>
</body>
</html>