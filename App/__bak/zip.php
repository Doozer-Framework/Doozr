<?php

/**
 * include DoozR bootstrapper
 */
require_once '../Framework/Core/DoozR.bootstrap.php';

/**
 * instanciate DoozR 
 */
$DoozR = DoozR_Core::getInstance();

/**
 * get module zip
 */
$zip = DoozR_Core::module('zip');

/**
 * 
 */
// Example. Zip all .html files in the current directory and send the file for Download.
$fileDir = 'D:/benjamincarl/';

$fileTime = date("D, d M Y H:i:s T");


$zip->setComment("Example Zip file.\nCreated on " . date('l jS \of F Y h:i:s A'));

$zip->addFile("Hello World!", "hello.txt");


if ($handle = opendir($fileDir)) {
    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle))) {
        if (strpos($file, ".png") !== false) {
            $pathData = pathinfo($fileDir . $file);
            $fileName = $pathData['filename'];

            $zip->addFile(file_get_contents($fileDir . $file), $file, filectime($fileDir . $file));
        }
    }
}

// here?
if (ini_get('zlib.output_compression')) {
    ini_set('zlib.output_compression', 'Off');
}

// Not scrictly necessary, the next line will call it anyway.
$zip->finalize();

$zipData = $zip->getZipData();

$length = strlen($zipData);

// send header
header('Pragma: public');
header("Last-Modified: " . $fileTime);
header("Expires: 0");
header("Accept-Ranges: bytes");
header("Connection: close");
header("Content-Type: application/zip");
header('Content-Disposition: attachment; filename="ZipExample.zip";' );
header("Content-Transfer-Encoding: binary");
header("Content-Length: ". $length);

echo $zipData; 

?>
