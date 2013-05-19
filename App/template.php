<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Template
 *
 **********************************************************************************************************************/

/**
 * Bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';


/**
 * Instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();


/**
 * Get registry containing DoozR's base object instances
 */
$registry = DoozR_Registry::getInstance();


/**
 * Get module session from Serviceloader
 */
$template = DoozR_Loader_Serviceloader::load('template', array(DOOZR_APP_ROOT.'Data/Private/Tpl/template.tpl'));
$i18n     = DoozR_Loader_Serviceloader::load('i18n', array($registry->config, 'de'));


/**
 * Store our PHPTAL compatible I18n module as translator
 */
$template->setTranslator($i18n);


/**
 * FROM HERE IT'S THE DEMONSTRATION FROM PHPTAL:
 * http://phptal.org/manual/en/#firstexample
 */

// the Person class
class Person {
    public $name;
    public $phone;

    function Person($name, $phone) {
        $this->name = $name;
        $this->phone = $phone;
    }
}

// let's create an array of objects for test purpose
$people = array();
$people[] = new Person("foo", "01-344-121-021");
$people[] = new Person("bar", "05-999-165-541");
$people[] = new Person("baz", "01-389-321-024");
$people[] = new Person("quz", "05-321-378-654");

// put some data into the template context
//$template->title = 'The title value';
$template->people = $people;

// execute the template
try {
    echo $template->execute();
}
catch (Exception $e){
    echo $e;
}

?>
