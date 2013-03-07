<?php

pre('I am a DoozR '.strtoupper(DoozR_Controller_Front::getInstance()->getRunningMode()).' App! and my argument foo\'s value is: ');

DoozR_Controller_Front::getInstance()->getRequest()->transform('request');

pre($_REQUEST->foo());

?>