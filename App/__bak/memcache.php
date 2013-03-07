<?php

//$memcache = new Memcache();
//$memcache->connect('localhost', 11211) or die ("Could not connect");

if (!extension_loaded('memcache')) {
    pred('Extension memcache not found!');
}


$key = md5('ddhfjshfjkhs');


$string = DoozR_Cache_Memcache::getInstance()->get($key);

if (!$string) {
    $string = 'Hallo Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metu';
    DoozR_Cache_Memcache::getInstance()->set($key, $string, MEMCACHE_COMPRESSED, 600);
} else {
    $string = 'cached: '.$string;
}


echo $string;



// Memcache singleton object
class DoozR_Cache_Memcache extends Memcache
{
    static private $m_objMem = null;

    static function getInstance()
    {
        if (!self::$m_objMem) {
            self::$m_objMem = new Memcache();

            // connect to the memcached
            self::$m_objMem->connect('127.0.0.1', 11211) or die ("The memcached server");
        }

        return self::$m_objMem;
    }
}

?>
