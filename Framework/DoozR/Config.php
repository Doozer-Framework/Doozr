<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Config
 *
 * Config.php - Config container for a Json reader (based on filesystem reader) to read Json configurations and
 * make use of three possible layers of caching [REQUEST -> [CACHE:RUNTIME] -> [CACHE:CONFIG] -> [CACHE:FILESYSTEM] ->
 * read from filesystem/network.
 * So lookup is (look in runtime cache) then (look in config stored in memory) then (look in memory for file) then
 * access filesystem/network the 1st time. Speedup!!!
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Config
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Facade/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Config/Interface.php';

/**
 * DoozR - Config
 *
 * Config container for a Json reader (based on filesystem reader) to read Json configurations and
 * make use of three possible layers of caching [REQUEST -> [CACHE:RUNTIME] -> [CACHE:CONFIG] -> [CACHE:FILESYSTEM] ->
 * read from filesystem/network.
 * So lookup is (look in runtime cache) then (look in config stored in memory) then (look in memory for file) then
 * access filesystem/network the 1st time. Speedup!!!
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Config
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @implements DoozR_Path,DoozR_Logger
 */
class DoozR_Config extends DoozR_Base_Facade_Singleton implements DoozR_Config_Interface
{
    /**
     * Default container of configuration
     * Our preferred type is JSON
     *
     * @var string
     * @access const
     */
    const DEFAULT_CONTAINER = 'Json';


    /**
     * Constructor.
     *
     * @param DoozR_Path_Interface   $path          An instance of DoozR_Path
     * @param DoozR_Logger_Interface $logger        An instance of DoozR_Logger
     * @param string                 $container     A container e.g. Json or Ini
     * @param bool                $enableCaching TRUE to enable internal caching, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Config
     * @access protected
     */
    protected function __construct(
        DoozR_Path_Interface   $path,
        DoozR_Logger_Interface $logger,
                               $container     = self::DEFAULT_CONTAINER,
                               $enableCaching = false
    ) {
        // create instance through factory and set as object to decorate!
        $this->setDecoratedObject(
            $this->factory(
                __CLASS__ . '_Container_' . ucfirst(strtolower($container)),
                $path->get('framework'),
                array(
                    $path,
                    $logger,
                    $enableCaching
                )
            )
        );
    }

    /**
     * Factory for creating an instance(s) of a config container.
     *
     * @param string $class     The name of class of the container
     * @param string $path      The base path to Framework
     * @param mixed  $arguments Arguments to pass to instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Config_Container_Json|DoozR_Config_Container_Ini The fresh created instance of Ini | Json | ...
     * @access protected
     */
    protected function factory($class, $path, $arguments = null)
    {
        include_once $path . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

        // create and return a fresh instance
        if ($arguments) {
            return $this->instanciate($class, $arguments);

        } else {
            return new $class();

        }
    }



    /**
     * Creates a configuration node.
     *
     * @param string $node The node to create
     * @param string $data The data to write to config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if entry was created successful, otherwise FALSE
     * @access public
     */
    public function create($node, $data)
    {
        return $this->getDecoratedObject()->create($node, $data);
    }

    /**
     * Reads and return a configuration node.
     *
     * @param mixed $node The node to read/parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from cache if successful, otherwise NULL
     * @access public
     */
    public function read($node)
    {
        return $this->getDecoratedObject()->read($node);
    }

    /**
     * Updates a configuration node.
     *
     * @param string $node  The configuration node
     * @param string $value The data to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if entry was created successful, otherwise FALSE
     * @access public
     */
    public function update($node, $value)
    {
        return $this->getDecoratedObject()->update($node, $value);
    }

    /**
     * Deletes a node.
     *
     * @param string $node The node to delete
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if entry was deleted successful, otherwise FALSE
     * @access public
     */
    public function delete($node)
    {
        return $this->getDecoratedObject()->delete($node);
    }


    /**
     * Setter for key => value pairs of config.
     *
     * @param string $node  The key used for entry
     * @param mixed  $value The value (every type allow) be sure to check if it is supported by your chosen config type
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function set($node, $value)
    {
        return $this->getDecoratedObject()->{$node} = $value;
    }

    /**
     * Getter for value of passed node.
     *
     * @param string $node The key used for value lookup.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|null The value of configuration node if set, otherwise NULL
     * @access public
     */
    public function get($node)
    {
        return $this->getDecoratedObject()->{$node};
    }
}
