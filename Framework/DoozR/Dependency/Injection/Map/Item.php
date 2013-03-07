<?php
/**
 * Items define how each dependency should be injected/maintained.
 *
 * Options
 *  Name - name of the dependency
 *  InjectWith - method, property, constructor
 *  InjectAs - depends on with param
 *  Force - bool, force injection
 *  NewClass - the name of the new class to create, false otherwise
 *
 * @author ryan
 */
class DoozR_Dependency_Injection_Map_Item {

    private $_dependencyName;
    private $_injectWith;
    private $_injectAs;
    private $_force = false;
    private $_newClass = null;

    public function setDependencyName($dependencyName) {
        $this->_dependencyName = $dependencyName;
        return $this;
    }

    public function setInjectWith($injectWith) {
        $this->_injectWith = $injectWith;
        return $this;
    }

    public function setInjectAs($injectAs) {
        $this->_injectAs = $injectAs;
        return $this;
    }

    public function setForce($force) {
        $this->_force = $force;
        return $this;
    }

    public function setNewClass($newClass) {
        $this->_newClass = $newClass;
        return $this;
    }

    public function dependencyName() {
        return $this->_dependencyName;
    }

    public function injectWith() {
        return $this->_injectWith;
    }

    public function injectAs() {
        return $this->_injectAs;
    }
    
    public function force() {
        return $this->_force;
    }

    public function newClass() {
        return $this->_newClass;
    }


}
