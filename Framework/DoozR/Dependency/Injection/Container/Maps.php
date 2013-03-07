<?php

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Dependency/Injection/Map.php';

/**
 * Holds all of the maps.
 *
 */
class DoozR_Dependency_Injection_Container_Maps {

    private $_maps = array();

    /**
     * Add/set a map to the container by name
     *
     * @param string $name
     * @param DoozR_Dependency_Injection_Map $map
     */
    public function set($name, $map) {
        $this->_maps[$name] = $map;
    }

    /**
     * Returns a dependency Map given a name
     *
     * @param string $name
     * @return DoozR_Dependency_Injection_Map
     */
    public function get($name) {
        if (isset($this->_maps[$name])) {
            return $this->_maps[$name];
        } else {
            return new DoozR_Dependency_Injection_Map();
        }
    }



}
