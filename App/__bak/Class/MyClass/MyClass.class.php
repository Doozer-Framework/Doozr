<?php

/**
 * Demonstration for a generic DoozR-Base based User-Class
 *
 * Enter description here ...
 * @author Ben
 *
 */

class MyClass extends DoozR_Base_Default
{
/*
    public function __construct()
    {
        // this is a simple constructor
        // it does nothing else than calling the demo() method
        $this->demo();
    }
*/

    public function demo()
    {
/*
        // 1st call a method n-times for profiling
        $this->profile($this, 'profilingExample1', array(1, 3), 100);

        // get result of profiling
        $profilingResult = $this->getProfilingDetails(false);

        // show result
        pre($profilingResult);

        // 2nd call a static method for n-times for profiling
        $this->profile($this, 'profilingExample2', null, 100);

        // get result of profiling
        $profilingResult2 = $this->getProfilingDetails(false);

        // show result
        pre($profilingResult2);

        // show path to this class
        pre($this->getPath());

        // show filename of this class
        pre($this->getFile());

        // show both before combined as path+file
        pre($this->getPathAndFile());

        // demo of logging
        $logging = $this->log('simple demo logging message!');

        // show status of logging
        pre($logging);
*/
    }


    public function profilingExample1($a, $b)
    {
        // senseless - just for demo
        return ($a*$b);
    }


    public static function profilingExample2()
    {
        // senseless - just for demo
        return 'DoozR';
    }
}

?>