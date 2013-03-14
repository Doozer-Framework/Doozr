<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Base Request
 *
 * Request.php - Base Request Class (e.g. as base for Web | Cli) of the DoozR Framework.
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
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Request
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

//require_once DOOZR_DOCUMENT_ROOT.'DoozR/Request/Securitylayer.php';

/**
 * DoozR Base Request
 *
 * Base Request Class (e.g. as base for Web | Cli) of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Request
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Base_Request // extends DoozR_Request_Securitylayer
{
    /**
     * holds the URL under which the current project operates
     *
     * @var string
     * @access protected
     */
    protected $url;

    /**
     * holds the TYPE of the request (can be WEB or CLI)
     *
     * @var string
     * @access protected
     */
    protected static $type;

    /**
     * Contains the GLOBALS already transformed to ojects to
     * prevent duplicate converting
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $initialized = array();

    /**
     * holds an instance/handle on logger
     *
     * @var object
     * @access protected
     */
    protected $logger;

    /**
     * The configuration of DoozR
     *
     * @var DoozR_Config
     * @access protected
     */
    protected $config;

    /**
     * holds translation matrix to transform php.ini values
     * to its global
     *
     * @var array
     * @access protected
     */
    protected $translationIniToGlobal = array(
        'G' => 'GET',
        'P' => 'POST',
        'C' => 'COOKIE',
        'S' => 'SESSION',
        'E' => 'ENVIRONMENT'
    );


    /**
     * Constructor of this class
     *
     * This method is the constructor of this class.
     *
     * @return void
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct()
    {
        // call Securitylayer's constructor to get PHPIDS + HTMLPurifier
        //parent::__construct();
    }

    /**
     * Returns the type of current request (web OR cli) as string
     *
     * @return string type of current request CLI or WEB (returns lowercase!)
     *
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public static function getType()
    {
        return self::$type;
    }

    /**
     * returns the request-method of current process
     *
     * Returns the method (POST / GET / PUT || CLI) of the current processed request.
     *
     * @return  string the method of current processed request (GET / POST / PUT || CLI)
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getRequestMethod()
    {
        if ($requestMethod = (isset($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : null) {
            return strtoupper($requestMethod);
        } else {
            return strtoupper(self::$type);
        }
    }

    /**
     * Transforms a given PHP-Global (e.g. SERVER [without "$_"]) to an object with an array interface
     *
     * This method is intend to transform a given PHP-Global (e.g. SERVER [without "$_"])
     * to an object with an array interface.
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function transform()
    {
        // get dynamic the sources
        $requestSources = array_change_value_case(func_get_args(), CASE_UPPER);

        // iterate over given sources
        foreach ($requestSources as $requestSource) {
            if (!in_array($requestSource, $this->_requestSources)) {
                throw new DoozR_Exception(
                    'Invalid request-source "$_'.$requestSource.'" passed to '.__METHOD__
                );
            }

            // build objects from global request array(s) like SERVER, GET, POST | CLI
            $this->transformToRequestObject($requestSource);
        }

        // successful transformed
        return true;
    }

    /**
     * Transforms a given global variable (e.g. _GET, _POST ...) to an object
     *
     * This method is intend to transforms a given global to an object and replace the original
     * PHP-Global with the new object.
     *
     * @param string $globalVariable The PHP-global to process (POST, GET, COOKIE, SESSION ...)
     *
     * @return  void
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    protected function transformToRequestObject($globalVariable)
    {
        // include Request_Parameter class right here
        include_once DOOZR_DOCUMENT_ROOT.'DoozR/Request/Parameter.php';

        // get prefix
        $globalVariable = $this->_addPrefix($globalVariable);

        // new way?
        $GLOBALS[$globalVariable] = new DoozR_Request_Parameter($globalVariable);
    }

    /**
     * checks if a given request-type (GET, POST, COOKIE) is part of a given collection of request-types
     *
     * This method is intend to check if a given request-type (GET, POST, COOKIE) is part of a given collection
     * of request-types
     *
     * @param array  $requesttypes An array of request-types
     * @param string $requesttype  The request type to lookup
     *
     * @return  boolean TRUE if it is part of collection, otherwise FALSE.
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    protected function containsRequesttype(array $requesttypes, $requesttype)
    {
        return in_array($requesttype, $requesttypes);
    }

    /**
     * checks if PHP's global can be build custom
     *
     * This method is intend to check if PHP's global can be build custom.
     *
     * @param array $sources The sources given for transformation
     *
     * @return  boolean TRUE if $_REQUEST can be build custom, otherwise FALSE
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    protected function checkForCustomRequest(array $sources)
    {
        // assume we build custom request
        $customRequest = true;

        // get GLOBAL parts (e.g. POST, GET ...) configured in php.ini as parts of $_REQUEST
        $requestOrder = $this->getRequestOrder();

        // if all needed parts exists, then we take these parts and build request of it later!
        foreach ($requestOrder as $source) {
            $customRequest = $customRequest && in_array($this->translateFromIniToGlobal($source), $sources);
        }

        // return status
        return $customRequest;
    }

    /**
     * translates values from php.ini (G, P, C) to PHP's globals (GET, POST, COOKIE)
     *
     * This method is intend to translate values from php.ini (G, P, C) to PHP's globals (GET, POST, COOKIE).
     *
     * @param string $value The value to translate
     *
     * @return  string The translated value
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    protected function translateFromIniToGlobal($value)
    {
        return isset($this->translationIniToGlobal[$value]) ? $this->translationIniToGlobal[$value] : null;
    }

    /**
     * returns the order of $_REQUEST from php.ini
     *
     * This method is intend to return the order of $_REQUEST from php.ini.
     *
     * @return  array The values in its order from php.ini
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    protected function getRequestOrder()
    {
        $orderFromIni = ini_get('request_order');
        $orderFromIni = ($orderFromIni) ? $orderFromIni : 'GP';
        return preg_split('//', $orderFromIni, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Returns input prefixed with an underscore
     *
     * This method is intend to add and underscore as prefix.
     *
     * @param string $value The string to add an underscore to
     *
     * @return  string The prefixed string
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _addPrefix($value)
    {
        // check if already prefixed
        if ($value == 'argv' || strpos($value, '_')) {
            return $value;
        }

        return '_'.$value;
    }
}

?>