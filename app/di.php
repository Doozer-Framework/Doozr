<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Core: Error- and Exception-Handling
 *
 **********************************************************************************************************************/

/**
 * bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';

/**
 * Instantiate DoozR
 */
$DoozR = DoozR_Core::getInstance();

/**
 * simple absolute path bootstrapping for better performance
 */
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Di/Bootstrap.php';

/**
 * Required classes (files) for static demonstration #3
 */
require_once DI_PATH_LIB_DI.'Collection.php';
require_once DI_PATH_LIB_DI.'Importer/Json.php';
require_once DI_PATH_LIB_DI.'Map/Static.php';
require_once DI_PATH_LIB_DI.'Factory.php';
require_once DI_PATH_LIB_DI.'Container.php';

/**
 * create instances of required classes
 * create instance of Di_Map_Annotation and pass required classes as arguments to constructor
 * The Di-Map builder requires two objects Collection + Importer
 */
$collection = new DoozR_Di_Collection();
$importer   = new DoozR_Di_Importer_Json();
$map        = new DoozR_Di_Map_Static($collection, $importer);


/**
 * generate map from input "data/map3.json"
 */
$map->generate(DOOZR_DOCUMENT_ROOT . 'Data/Private/Config/.dependencies');



$Database1 = new stdClass();
$Logger1   = new stdClass();


class Foo
{
    public $data = array('foo', 'bar', 'baz');
}


class Bar
{
    protected $data;

    public function __construct(Foo $data)
    {
        $this->data = $data;
    }

    public function test()
    {
        echo 'Foo Bar '.var_export($this->data, true);
    }
}


/**
 * wire the instances automagically for class "Foo" (and all others?)
 */
$map->wire();


/**
 * create instances of required classes
 * create instance of Di_Container and set factory created previously
 */
$factory    = new DoozR_Di_Factory();
$container  = DoozR_Di_Container::getInstance();
$container->setFactory($factory);


/**
 * store previously created dependency map in container
 */
$container->setMap($map);


/**
 * Everything should be in the right position. We create an instance of
 * class "Bar" now.
 */
$Bar = $container->build('Bar');


/**
 * Test our created instance by calling method test()
 */
$Bar->test();


/**
 * Check against instance
 */
if (get_class($Bar) === 'Bar') {
    echo '<pre>Successfully created instance of class Bar.</pre>';
}


/**
 * Debug output
 */
echo '<pre>';
var_dump($Bar);
echo '</pre>';


/**
 * Now build a second instance of class Bar
 */
$Bar2 = $container->build('Bar');

/**
 * Test our created instance by calling method test()
 */
$Bar2->test();


/**
 * Check against instance
 */
if (get_class($Bar2) === 'Bar') {
    echo '<pre>Successfully created instance of class Bar.</pre>';
}


/**
 * Debug output
 */
echo '<pre>';
var_dump($Bar2);
echo '</pre>';


/**
 * Check that we got two different instances
 */
if ($Bar !== $Bar2) {
    echo '<pre>Everything seems to works fine. We retrieved two separate instances.</pre>';
}

?>

<p>
    <a href="index.php#Demonstration">Back to index</a>
</p>
