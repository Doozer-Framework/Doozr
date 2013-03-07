<?php

/**
 * A map is just a bunch of items
 *
 */
class DoozR_Dependency_Injection_Map {

    /**
     * Holds an array of items
     *
     * @var array
     */
    private $_items = array();

    /**
     * @param Base_Di_Map_Item $item
     */
    public function add($item) {
        $this->_items[] = $item;
    }

    /**
     * @return array
     */
    public function items() {
        return $this->_items;
    }

    /**
     * Returns an array of items based on the injectWith
     *
     * @param string injectWith return an array of only items that match injectWith
     * @return array
     */
    public function itemsFor($injectWith) {

        $return = array();
        foreach ($this->_items as $item) {
            if ($item->injectWith() == $injectWith) {
                $return[] = $item;
            }
        }
        return $return;

    }

    /**
     * Checks to see if the map has dependencies for
     * $injectWith (injection with = method, constructor, etc)
     *
     *
     * @param string $injectWith method, constructor, property, etc
     * @return bool
     */
    public function has($injectWith) {
        if (count($this->itemsFor($injectWith)) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return int number of items
     */
    public function count() {
        return count($this->_items);
    }

}

