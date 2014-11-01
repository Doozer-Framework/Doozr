<?php

/**
 * Demo Model
 */
final class Model_Index extends DoozR_Base_Model
{
    /**
     * Magic & generic data delivery.
     *
     * @author Benjamin Carl <benjamin.carl@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE (we need!!! this as signal from userland back to backend!)
     * @access protected
     */
    protected function __data()
    {
        // Dummy array for iterated output
        $people = array();
        $people[] = 'John Do';
        $people[] = 'Jane Do';
        $people[] = 'Foo Bar';
        $people[] = 'Bar Baz';

        // Data used as key => value pairs for template engine
        $data = array(
            'title'  => 'DoozR\'s bootstrap environment',
            'year'   => date('Y'),
            'people' => $people,
        );

        // Just store and trigger dispatch to view -> render by observer pattern
        $this->setData($data);

        // Successful
        return true;
    }
}
