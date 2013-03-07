<?php

/**
 * The parser takes a string and reads all of the PdInject
 * information.  Its an internal class, no reason for the user
 * to need to parse his own strings.
 *
 */

class DoozR_Dependency_Injection_Map_Builder_Parser
{

    private $_string;
    private $_info;

    private $_matches = array();
    private $_matched = 0;

    private $_options = array();

    /**
     * The string to parse
     *
     * @param string $string
     */
    public function setString($string) {
        $this->_string = $string;
    }

    /**
     * Any information on whats about to be parsed.  Usually this is a
     * Reflection method/property, but it can be anything.  Used when
     * throwing errors/exceptions or debugging.
     *
     * @param mixed $info
     */
    public function setInfo($info) {
        $this->_info = $info;
    }

    /**
     * Matches the string
     */
    public function match() {
        $this->_matched = preg_match_all(
                '/@PdInject(.*?)(\n|$)/i',
                $this->_string,
                $this->_matches
        );

        //var_dump($this->_matches);

    }

    /**
     * Checks if the string has a PdInject command
     *
     * @return bool
     */
    public function hasCommand() {
        return $this->_matched > 0;
    }

    /**
     * Return ths number of PdInject commands
     *
     * @return int
     */
    public function numberOfCommands() {
        return count($this->_matches[1]);
    }

    private function _defaultOptions() {
        return array(
            'dependencyName' => null,
            'force' => false,
            'injectWith' => null,
            'injectAs' => null,
            'newClass' => false,
        );
    }

    /**
     * This function builds an array of options
     * for each of the commands that were matched.
     * This options array is readable/similar to
     * a dependency map item.
     *
     * Uglyish function.  we should probably refactor.
     *
     * I hate if/else statements.  They add too much
     * complexity.  Also we need some better way
     * of reading the option/values for the
     * with:as params.
     *
     */
    public function buildOptions() {

        foreach ($this->_matches[1] as $command) {

            $command = trim($command);
            $options = $this->_defaultOptions();

            if ($command != "") {

                /*
                 * Valid Commands look something like this
                 *
                 * @PdInject new:Class
                 * @PdInject DependencyName
                 * @PdInject DependencyName method:setName force:true
                 * @PdInject DependencyName property:name
                 * @PdInject DependencyName constructor:1
                 *
                 */

                $params = explode(" ", $command);

                for ($i = 0; $i < count($params); $i++) {

                    $parts = explode(":", $params[$i]);

                    if (count($parts) != 2) {

                        if ($i == 0) {
                            // dependency name
                            $options['dependencyName'] = $params[$i];
                        } else {
                            throw new Exception('Invalid option (' . $params[$i] .'). Correct syntax is Option:Value.  Info: ' . $this->_info);
                        }

                    } else {
                        // option

                        $key = $parts[0];
                        $value = $parts[1];


                        if ($key == 'force') {
                            $options['force'] = $value;
                        } elseif ($key == 'new') {
                            $options['newClass'] = $value;
                        } else {
                            $options['injectWith'] = $key;
                            $options['injectAs'] = $value;
                        }

                    }

                }

            }

            // congrats, you made it out of nesting hell alive

            $this->_options[] = $options;

        }
    }

    /**
     * Returns all of the options, an an array.
     *
     * And option is an array of data
     *
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }


}
