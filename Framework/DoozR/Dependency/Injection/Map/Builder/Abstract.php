<?php

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Dependency/Injection/Map.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Dependency/Injection/Map/Item.php';

/**
 * Abstract class for building maps
 *
 *
 */
abstract class DoozR_Dependency_Injection_Map_Builder_Abstract
{
    protected abstract function _setup();
    protected abstract function _build();

    /**
     * @var DoozR_Dependency_Injection_Map
     */
    protected $_map;

    /**
     * The map
     *
     * @return DoozR_Dependency_Injection_Map
     */
    public function map() {
        return $this->_map;
    }

    /**
     * Setup for building a make.  Makes a new
     * DoozR_Dependency_Injection_Map and then runs the builders _setup method
     */
    public function setup() {
        $this->_map = new DoozR_Dependency_Injection_Map();
        $this->_setup();
    }

    /**
     * Builds the map by running the setup method and
     * then running the builers _build method.
     *
     */
    public function build() {
        $this->setup();
        $this->_build();
    }

    /**
     * Creates a Map Item based off options array
     *
     * @param array $options
     * @return DoozR_Dependency_Injection_Map_Item
     */
    protected function _makeItemFromOptions($options) {

        $defaults = array(
            'dependencyName' => null,
            'injectWith' => null,
            'injectAs' => null,
            'force' => false,
            'newClass' => null,
        );

        $options = array_merge($defaults, $options);

        $item = new DoozR_Dependency_Injection_Map_Item();
        $item->setDependencyName($options['dependencyName']);
        $item->setInjectWith($options['injectWith']);
        $item->setInjectAs($options['injectAs']);
        $item->setForce($options['force']);
        $item->setNewClass($options['newClass']);

        return $item;
    }

}