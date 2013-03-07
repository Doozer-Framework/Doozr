<?php

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Dependency/Injection/Map/Builder/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Dependency/Injection/Map/Builder/Parser.php';

/**
 * This class will read a class and build a dependency map
 * (of items) based off the doc blocks.
 *
 */

class DoozR_Dependency_Injection_Map_Builder_Class extends DoozR_Dependency_Injection_Map_Builder_Abstract {

    private $_class;

    /**
     * @var ReflectionClass
     */
    private $_reflect;

    /**
     * Set a class name
     *
     * @param string $class
     */
    public function setClass($class) {
        $this->_class = $class;
    }

    /**
     * Sets up the builder for... building.
     *
     * Makes a new map and reflection class
     *
     */
    protected function _setup() {
        $this->_reflect = new ReflectionClass($this->_class);
    }

    /**
     * Runs all the builders and builds the entire map
     */
    protected function _build() {
        $this->buildMethods();
        $this->buildProperties();
        $this->buildClass();

    }

    /**
     * Pass in a reflection item (class, property, method)
     * and this function will build a parser and return its
     * results.
     *
     * @param ReflectionClass $classProperty
     * @return array all options
     */
    private function _optionsFrom($classProperty) {
        $parser = new DoozR_Dependency_Injection_Map_Builder_Parser();
        $parser->setString($classProperty->getDocComment());
        $parser->setInfo($classProperty);
        $parser->match();
        $parser->buildOptions();

        return $parser->getOptions();
    }

    /**
     * Loops through all of the methods and builds items/maps
     * for them.
     */
    public function buildMethods() {

        $methods = $this->_reflect->getMethods();

        foreach($methods as $method) {

            foreach ($this->_optionsFrom($method) as $options) {

                if ($method->getName() == '__construct') {
                    $options['injectWith'] = 'constructor';
                } else {
                    $options['injectWith'] = 'method';
                    $options['injectAs'] = $method->getName();
                }

                $this->_map->add(
                        $this->_makeItemFromOptions($options)
                );

            }

        }
    }

    /**
     * Loops through all of the properties and builds items/maps
     * for them.
     */
    public function buildProperties() {

        $properties = $this->_reflect->getProperties();

        foreach ($properties as $property) {

            foreach ($this->_optionsFrom($property) as $options) {

                $options['injectWith'] = 'property';
                $options['injectAs'] = $property->getName();

                $this->_map->add(
                        $this->_makeItemFromOptions($options)
                );

            }

        }

    }

    /**
     * Builds items based on the classes doc block
     */
    public function buildClass() {

        foreach ($this->_optionsFrom($this->_reflect) as $options) {

            $this->_map->add(
                    $this->_makeItemFromOptions($options)
            );

        }

    }



}
