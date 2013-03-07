<?php

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Dependency/Injection/Container/Maps.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Dependency/Injection/Container/Dependencies.php';


class DoozR_Dependency_Injection_Container
{
    private static $_instance = array();

    /**
     * @var DoozR_Dependency_Injection_Container_Maps
     */
    private $_maps;

    /**
     * @var DoozR_Dependency_Injection_Container_Dependencies
     */
    private $_dependencies;

    private $_name;


    /**
     * Returns one instance singleton
     *
     * @return DoozR_Dependency_Injection_Container
     */
    public static function get($container = 'main') {

        if (!isset(self::$_instance[$container])) {
            self::$_instance[$container] = new self();
            self::$_instance[$container]->setName($container);
            self::$_instance[$container]->setup();
        }

        return self::$_instance[$container];

    }

    public function setName($name) {
        $this->_name = $name;
    }

    public function name() {
        return $this->_name;
    }

    /**
     * @return DoozR_Dependency_Injection_Container_Maps
     */
    public function maps() {
        return $this->_maps;
    }

    /**
     * @return DoozR_Dependency_Injection_Container_Dependencies
     */
    public function dependencies() {
        return $this->_dependencies;
    }

    /**
     * Sets up the container by creating a new map
     * and dependency holder.  This function doesn't really
     * need to ever be called, since the get() function
     * calls it when creating a 'new' container.
     */
    public function setup() {
        $this->_maps = new DoozR_Dependency_Injection_Container_Maps();
        $this->_dependencies = new DoozR_Dependency_Injection_Container_Dependencies();
    }


    private function __construct()
    {
		// prevent direct instanciation
    }

    private function __clone()
    {
		// prevent cloning
    }
}

?>
