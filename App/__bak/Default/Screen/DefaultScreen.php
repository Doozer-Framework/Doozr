<?php

pre('I am a DoozR '.strtoupper(DoozR_Core::front()->getRunningMode()).' App! and my argument foo\'s value is: ');
pre($_REQUEST->foo());

?>