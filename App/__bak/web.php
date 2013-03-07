<?php
define("GORMDB_FORCE_DEBUG",true);

/**
 * retrieve path to user's (app) DoozR - config(s)
 */
require_once 'include/pathfinder.include.php';

// require the bootstrapper for DoozR
require_once '../Framework/Core/DoozR.bootstrap.php';

// get an instance of DoozR
$DoozR = DoozR_Core::getInstance();
$dbHandle = DoozR_Core::model();

$container = $dbHandle->getContainer("user");
$rows = $container->findByLoginAndHash('simon','test', true);

#$rows[0]->delete();

pre($rows);


echo "<BR>END";
?>
