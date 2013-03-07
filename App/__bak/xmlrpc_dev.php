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
 * get module form
 */
$xmlrpc = DoozR_Core::module('xmlrpc');


// https://...' bei SSL-Verschlüsselung
$xmlrpc->setServiceUrl('http://evatr.bff-online.de');

$UstId_1    = 'DE126229693';

/*
// SET 1
$UstId_2    = 'IE6388047A';
$Firmenname = 'Google Ireland Ltd.';
$Ort        = 'Dublin';
$PLZ        = '4';
$Strasse    = 'Barrow Street';
*/

// SET 2
$UstId_2    = 'ATU33864707';
$Firmenname = 'Red Bull GmbH';
$Ort        = 'Fuschl am See';
$PLZ        = '5330';
$Strasse    = 'Am Brunnen 1';

$Druck      = 'nein';

// get result form rpc-call
$result = $xmlrpc->query(
        'evatrRPC',
        $UstId_1,
        $UstId_2,
        $Firmenname,
        $Ort,
        $PLZ,
        $Strasse,
        $Druck
    );

if (!$result) {
    die('Ein Fehler ist aufgetreten - '.$xmlrpc->getErrorCode().":".$xmlrpc->getErrorMessage());
}

$outString = '<pre>'.$xmlrpc->getResponse().'</pre>';

echo $outString;

?>
