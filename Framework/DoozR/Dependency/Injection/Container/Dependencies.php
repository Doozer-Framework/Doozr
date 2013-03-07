<?php

/**
 * Holds all of the dependencies in an array by
 * name (key) and dependency (value).
 *
 */
class DoozR_Dependency_Injection_Container_Dependencies
{
    private $_dependencies = array();

    /**
     * Returns a dependency by name.  If dependency is not found,
     * null is returned.
     *
     * @param string $name
     * @return mixed dependency
     */
    public function get($name) {

        if (isset($this->_dependencies[$name])) {
            return $this->_dependencies[$name]['instance'];
        } else {
            return null;
        }

    }

    /**
     * Sets a depenedency by name
     *
     * @param string $name
     * @param mixed $dependency resource
     */
    public function set($name, $dependency) {
        $this->_dependencies[$name] = array(
            'instance' => $dependency,
        );
    }

}