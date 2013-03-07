<?php

// include core class
/**
 * include DoozR bootstrapper
 */
require_once '../../../Framework/Core/DoozR.bootstrap.php';

/**
 * instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();


$xmlrpc = DoozR_Core::module('xmlrpc');


$UstId_1    = 'DE813502041';
$UstId_2    = 'DE813502041';
$Firmenname = 'upside relationship marketing GmbH';
$Ort        = 'Düsseldorf';
$PLZ        = '40212';
$Strasse    = 'Marienstr. 14';
$Druck      = 'nein';


$param = array(php_xmlrpc_encode($UstId_1),
php_xmlrpc_encode($UstId_2),
php_xmlrpc_encode($Firmenname),
php_xmlrpc_encode($Ort),
php_xmlrpc_encode($PLZ),
php_xmlrpc_encode($Strasse),
php_xmlrpc_encode($Druck)
              );

//php_xmlrpc_encode($stateno)
$f=new xmlrpcmsg('evatrRPC', $param);


print "<pre>Sending the following request:\n\n" . htmlentities($f->serialize()) . "\n\nDebug info of server data follows...\n\n";

$c=new xmlrpc_client("/", "evatr.bff-online.de", 80);

$c->setDebug(1);

$r=&$c->send($f);

if(!$r->faultCode())
{
    $v = $r->value();
    print htmlspecialchars($v->scalarval()) . "</pre><br/>";
    // print "<HR>I got this value back<BR><PRE>" .
    //  htmlentities($r->serialize()). "</PRE><HR>\n";
}
else
{
    print "An error occurred: ";
    print "Code: " . htmlspecialchars($r->faultCode())
        . " Reason: '" . htmlspecialchars($r->faultString()) . "'</pre><br/>";
}

?>
