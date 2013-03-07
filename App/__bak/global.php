<?php

    require_once '../Framework/Core/DoozR.bootstrap.php';
	
	// get an instance of DoozR
    DoozR_Core::getInstance();
    
    // demo - access through PROPERTY 
    pre($_GET->foo);

    // demo - access through METHOD (default = sanitized)
    pre($_GET->foo());
	
    // demo - access through METHOD (raw)
    pre($_GET->foo(true));	

    // demo - access through METHOD (get() = sanitized)
    pre($_GET->get('foo')->get());

    // demo - access through METHOD (getRaw())
    pre($_GET->get('foo')->getRaw());

?>
