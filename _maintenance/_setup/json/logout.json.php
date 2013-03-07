<?php

// include DoozR core
require_once '../../Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance('');

$auth = $DoozR->getModuleHandle('auth');


$auth_array = array(
    'store'		   => 'file',
    'action'	   => 'delete',
    'regensession' => true
);

$ok = $auth->dispatch($auth_array);

// return json formatted result
echo json_encode(array('success'=>$ok, 'error'=>$auth->getLastError()));

?>
