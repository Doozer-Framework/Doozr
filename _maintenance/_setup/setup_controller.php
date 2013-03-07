<?php

// include DoozR core
require_once '../Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance('');

require_once $DoozR->getPathBase() . '_setup/class/setup.class.php';

$setup = new Setup($DoozR);

echo $setup->dispatch();

?>
