<?php

require_once '../Core/Controller/Core.php';

$DoozR = DoozR_Core::getInstance();

$db = $DoozR->getModuleHandle('db');

$sql = "SELECT * FROM user";

$res = $db->query($sql);

echo $res;

?>
