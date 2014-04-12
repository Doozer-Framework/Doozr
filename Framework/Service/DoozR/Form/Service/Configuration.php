<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Configuration.php - Configuration class for sharing configurations.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 *   must display the following acknowledgement: This product includes software
 *   developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR - Form - Service - Shared Constant(s)
 *
 * Configuration class for sharing configurations.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id:$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Form_Service_Configuration
{
    /**
     * The id.
     *
     * @var string
     * @protected
     */
    protected $id;

    /**
     * The input.
     *
     * @var mixed
     * @protected
     */
    protected $input;

    /**
     * The hash/checksum of this class.
     *
     * @var string
     * @protected
     */
    protected $hash;

    /**
     * Dirty flag.
     *
     * @var boolean
     * @protected
     */
    protected $dirty = false;

    /**
     * The prefix which identifies DoozR's commands in configurations
     *
     * @var string
     * @access public
     * @const
     */
    const DOOZR_COMMAND_PREFIX = 'doozr-';


    /*-----------------------------------------------------------------------------------------------------------------*
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Getter for id.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The id
     * @access public
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter for input.
     *
     * @param mixed $input The input to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    /**
     * Getter for input.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The input
     * @access public
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Getter for hash.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The hash, or NULL if not set
     * @access public
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Getter for configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return stdClass The configuration
     * @access public
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Getter for dirty state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if dirty, FALSE if not
     * @access public
     */
    public function getDirty()
    {
        return $this->dirty;
    }

    /**
     * Parses a configuration from an array to provide access via object.
     *
     * @param array $configuration The configuration to parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Configuration Current instance for chaining
     * @access public
     */
    public function parseFromArray(array $configuration)
    {
        $this->validateInput($configuration);

        $this->setInput($configuration);

        $this->parse($configuration);

        $this->setId($this->getHash());

        return $this;
    }

    /**
     * Magic wrapper acts as generic setter and getter.
     *
     * @param string $method   The method called (e.g. setFoo('bar') or getFoo()
     * @param array  $argument The argument for setter (e.g. 'bar')
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Result of operation
     * @access public
     */
    public function __call($method, $argument = array())
    {
        $method = str_split_camelcase($method);

        switch (strtolower($method[0])) {
            case 'get':
                return $this->configuration->{strtolower($method[1])};
                break;

            case 'set':
                return $this->configuration->{strtolower($method[1])} = $argument[0];
                break;

            default:
                return null;
                break;
        }
    }

    /**
     * Magic __setter.
     *
     * @param string $variable The variable to set
     * @param mixed  $value    The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __set($variable, $value)
    {
        $this->{$variable} = $value;
        $this->updateHash();
    }

    /**
     * Magic __getter.
     *
     * @param string $variable The variable to return
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value of the variable
     * @access public
     */
    public function __get($variable)
    {
        return (isset($this->{$variable})) ? $this->{$variable} : null;
    }

    /*-----------------------------------------------------------------------------------------------------------------*
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Validates the input passed to parser.
     *
     * @param array $input The input to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE
     * @access protected
     * @throws \Exception
     */
    protected function validateInput($input)
    {
        // check
        $keys = array(
            'form',
            'elements',
        );

        switch (gettype($input)) {
            case 'array':
                foreach ($keys as $key) {
                    if (array_key_exists($key, $input) === false) {
                        throw new \Exception(
                            'Input invalid! Please make sure that input array contains keys (form, elements).'
                        );
                    }
                }
                break;
        }

        return true;
    }

    /**
     * Updates the current hash.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function updateHash()
    {
        $this->setHash(sha1(serialize($this->configuration)));
    }

    /**
     * Checks a string for command prefix and return true or false.
     *
     * @param string $value The value to check for command
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if command was found, otherwise FALSE
     * @access protected
     */
    protected function containsCommand($value)
    {
        return (strpos($value, self::DOOZR_COMMAND_PREFIX) === 0);
    }

    /**
     * Returns the method name of a DoozR function by passed key.
     *
     * @param string $key The key to return method name for
     *
     * @return string
     */
    protected function getMethodByKey($key)
    {
        $method = explode('-', str_replace(self::DOOZR_COMMAND_PREFIX, '', $key));

        foreach ($method as $key => $node) {
            $method[$key] = ucfirst($node);
        }

        return 'set' . implode('', $method);
    }

    /**
     * Parses a configuration.
     *
     * @param mixed $configuration Configuration to parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    protected function parse($configuration)
    {
        $result = array();

        // Extract commands of DoozR from configuration
        foreach($configuration as $root => $childs) {

            foreach ($childs as $key => $values) {

                foreach ($values as $node => $value) {

                    $dom   = array();
                    $doozr = array();

                    switch ($node) {
                        case 'tag':
                            $tag = $value;
                            break;
                        case 'properties':
                            $properties = $value;

                            // Get commands of form
                            foreach ($properties as $property => $value) {
                                if ($this->containsCommand($property) === true) {

                                    $doozr[] = array(
                                        'method' => $this->getMethodByKey($property),
                                        'value'  => $value,
                                    );
                                } else {
                                    $dom[$property] = $value;
                                }
                            }
                            break;
                    }
                }

                if (isset($result[$root]) === false) {
                    $result[$root] = array();
                }

                $result[$root][] = array(
                    'dom'   => $dom,
                    'doozr' => $doozr,
                );
            }
        }

        // After iterating all parts with the same logic shift child form to root -> only one possible!
        $result['form'] = $result['form'][0];

        $this->setConfiguration(array_to_object($result));
    }

    /**
     * Setter for id.
     *
     * @param string $id The id to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Setter for hash.
     *
     * @param string $hash The hash to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Setter for dirty.
     *
     * @param booelean TRUE or FALSE dirty state
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setDirty($state)
    {
        $this->dirty = $state;
    }

    /**
     * Setter for configuration.
     *
     * @param stdClass $configuration The configuration to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConfiguration(stdClass $configuration)
    {
        $this->configuration = $configuration;
        $this->updateHash();
        $this->setDirty(true);
    }
}
