<?
declare(ticks = 1);

// include DoozR core
require_once 'Controller/Core/Core.php';

$DoozR = DoozR_Core::getInstance();
$cronjob = $DoozR->getModuleHandle("cronjob");

$cronjob->setPidFile("./test.pid");
$cronjob->setPID(posix_getpid());
$cronjob->createPID();

while(!Cronjob::getTerminate() ) {
    sleep(2);
}

$cronjob->releasePID();
?>
