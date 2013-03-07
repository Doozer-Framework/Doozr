<?php

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Dependency/Injection/Make/Abstract.php';

/**
 * Injects (read: setter injection) all of the dependencies
 * into the object.
 *
 */
class DoozR_Dependency_Injection_Make_Setter extends DoozR_Dependency_Injection_Make_Abstract
{
    /**
     * Injects all of the properties and methods
     */
    public function injectObject()
    {
        // load the map
        $this->loadMap();
        $this->_injectMethods();
        $this->_injectProperties();
    }

    private function _injectMethods()
    {
        /* @var $item DoozR_Dependency_Injection_Map_Item */

        foreach ($this->_map->itemsFor('method') as $item) {
            // only inject if the class has the method, or the item allows forcing
            $reflector = new ReflectionClass($this->_className);
            if ($reflector->hasMethod($item->injectAs()) || $item->force()) {
                $this->_object->{$item->injectAs()}($this->getDependencyForItem($item));
            }
        }
    }

    private function _injectProperties()
    {
        /* @var $item DoozR_Dependency_Injection_Map_Item */
        foreach ($this->_map->itemsFor('property') as $item) {
            // only inject if the class has the property, or the item allows forcing
            $reflector = new ReflectionClass($this->_className);
            if ($reflector->hasProperty($item->injectAs()) || $item->force()) {
                $this->_object->{$item->injectAs()} = $this->getDependencyForItem($item);
            }
        }
    }

    /**
     * Injects everything into the passed object/instance
     *
     * @param mixed $object instance
     * @param string $containerName the container that holds the maps/dependencies
     */
    public static function inject($object, $containerName = 'main')
    {
        $injector = new self();
        $injector->setObject($object);
        $injector->setContainer(DoozR_Dependency_Injection_Container::get($containerName));
        $injector->injectObject();
    }
}
