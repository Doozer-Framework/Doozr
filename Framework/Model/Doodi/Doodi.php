<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Doodi - Database-Wrapper (DoozR's-Object-Oriented-Database-Interface)
 *
 * Doodi.php - This class is the front API of Doodi. Doodi is a acronym for
 * "DoozR's-Object-Oriented-Database-Interface". Doodi can be used with any
 * existing OxM (x = R, D, ...). You only need to generate the Route and the
 * container classes with the tool "RouteGenerator.php".
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
 * @package    DoozR_Model
 * @subpackage DoozR_Model_Doodi
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Facade/Singleton/Strict.php';

/**
 * DoozR - Doodi - Database-Wrapper (DoozR's-Object-Oriented-Database-Interface)
 *
 * This class is the front API of Doodi. Doodi is a acronym for
 * "DoozR's-Object-Oriented-Database-Interface". Doodi can be used with any
 * existing OxM (x = R, D, ...). You only need to generate the Route and the
 * container classes with the tool "RouteGenerator.php".
 *
 * @category   DoozR
 * @package    DoozR_Model
 * @subpackage DoozR_Model_Doodi
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
final class Doodi extends DoozR_Base_Facade_Singleton_Strict
{
    /**
     * holds the driver (mode) in which Doodi operates (e.g. couchdb or mysql)
     *
     * @var string
     * @access private
     */
    private $_driver = null;

    /**
     * holds the hostname
     *
     * @var string
     * @access private
     */
    private $_host = null;

    /**
     * holds the port
     *
     * @var integer
     * @access private
     */
    private $_port = null;

    /**
     * holds the user
     *
     * @var string
     * @access private
     */
    private $_user = null;

    /**
     * holds the password
     *
     * @var string
     * @access private
     */
    private $_password = null;

    /**
     * holds the database (name)
     *
     * @var string
     * @access private
     */
    private $_database = null;

    /**
     * holds the path to this class
     *
     * @var string
     * @access private
     */
    private $_path;

    /**
     * holds the transformer class if exist
     *
     * @var mixed
     * @access private
     * @static
     */
    public static $transformer = null;

    /**
     * holds the original-configuration
     *
     * @var array
     * @access private
     */
    private $_configuration;


    /*******************************************************************************************************************
     * // BEGIN MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/

    /**
     * Constructor builds the class
     *
     * @param array $configuration The configuration for the Database-Connection
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access protected
     */
    protected function __construct($configuration = null)
    {
        // store retrieved data
        $this->_driver   = $configuration['DRIVER'];
        $this->_host     = $configuration['HOST'];
        $this->_port     = $configuration['PORT'];
        $this->_user     = $configuration['USER'];
        $this->_password = $configuration['PASSWORD'];
        $this->_database = $configuration['DATABASE'];

        // store path to this class
        $this->_path = $this->getPath();

        // store the original configuration
        $this->_configuration = $configuration;

        // check for transformer
        $this->_initTransformer();
    }

    /*******************************************************************************************************************
     * \\ END MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN TOOLS + HELPER
     ******************************************************************************************************************/

    /**
     * This method is intend to initialize the transformer-lass if exists
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @throws Exception
     */
    private function _initTransformer()
    {
        // mode
        $mode = ucfirst($this->_driver);

        // transformation exists?
        $transformationFile = $this->_path.'Transformation'.DIRECTORY_SEPARATOR.$mode.'.php';

        // check for transformation-class-file
        if (file_exists($transformationFile)) {
            // include if exist
            include_once $transformationFile;

            // combine parts to transformation classname
            $transformerClass = 'Transformation_'.$mode;

            // and instanciate the transformer
            self::$transformer = new $transformerClass($this->_configuration);
        } else {
            // no transformer => no function
            throw new Exception(
                'No "Transformer ('.$transformationFile.')" for Mode: "'.$mode.'" // Driver: "'.$this->_driver.
                '" found. Can\'t continue!'
            );
        }
    }

    /**
     * returns the plain-method name from given __METHOD__-constant
     *
     * This method is intend to return the plain-method name from given __METHOD__-constant
     *
     * @param string $method The method to return plain name from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Plain method-name
     * @access private
     */
    private function _getMethod($method)
    {
        return str_replace(__CLASS__.'::', '', $method);
    }

    /**
     * This method is intend to return the arguments of a method if exist, otherwise null
     *
     * @param array $arguments The arguments of a method retrieved by func_get_args()
     *
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @return mixed ARRAY arguments if exist, otherwise NULL
     * @access private
     */
    private function _getArguments($arguments)
    {
        return (!empty($arguments)) ? $arguments : null;
    }

    /*******************************************************************************************************************
     * \\ END TOOLS + HELPER
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN PUBLIC INTERFACE (SIMPLE ACCESS TO DATABASE VIA CRUD-PRINCIPLE)
     ******************************************************************************************************************/

    /**
     * This method is intend to return the transformer instance of this class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed OBJECT the transformer-instance if exist, otherwise NULL
     * @access public
     */
    public static function getTransformer()
    {
        return self::$transformer;
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC INTERFACE (SIMPLE ACCESS TO DATABASE VIA CRUD-PRINCIPLE)
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN AUTOMATIC TEAR DOWN
     ******************************************************************************************************************/

    /**
     * This method is intend to setup and call generic singleton-getter and return an instance
     * of the requested class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object instance/object of this class
     * @access public
     */
    public function __destruct()
    {
        // close open database
        $this->close();

        // disconnect from server
        $this->disconnect();

        // finally call parents destructor
        parent::__destruct();
    }

    /*******************************************************************************************************************
     * \\ END AUTOMATIC TEAR DOWN
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN SINGLETON PATTERN INSTANCE GETTER
     ******************************************************************************************************************/

    /**
     * This method is intend to setup and call generic singleton-getter and return an instance
     * of the requested class.
     *
     * @param array $configuration The configuration for the Database-Connection
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object instance/object of this class
     * @access public
     */
    public static function getInstance($configuration = null)
    {
        // dispatch to parent
        return parent::getInstance($configuration);
    }

    /*******************************************************************************************************************
     * \\ END SINGLETON PATTERN INSTANCE GETTER
     ******************************************************************************************************************/
}

?>
