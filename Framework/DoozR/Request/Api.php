<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Request - Api
 *
 * Api.php - Container for preprocessed API request data.
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
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Api
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Exception.php';

/**
 * DoozR - Request - Api
 *
 * Container for preprocessed API request data.
 *
 * @category   DoozR
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Api
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Request_Api
{
    /**
     * Arguments passed to script via GET, POST, CLI ...
     *
     * @var DoozR_Request_Parameter
     * @access public
     */
    public $arguments;

    /**
     * Method (verb) used to request data (GET, POST, ...)
     *
     * @var string
     * @access public
     */
    public $method;

    /**
     * The original unmodified request as array
     *
     * @var array
     * @access public
     */
    public $originalRequest;

    /**
     * The current processed request (after redirects ...)
     *
     * @var array
     * @access public
     */
    public $request;

    /**
     * The requested resource/endpoint
     *
     * @var string
     * @access public
     */
    public $resource;

    /**
     * The URL of the current request
     *
     * @var string
     * @access public
     */
    public $url;


    /**
     * Extracts variables from current requests URL
     *
     * @param string  $pattern  The pattern to use for extracting variables from URL (e.g. /{{foo}}/{{bar}}/
     * @param closure $callback The callback/closure to execute
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed
     * @access public
     * @throws DoozR_Exception
     */
    public function get($pattern, $callback = null)
    {
        // check for required url
        if ($this->url === null) {
            throw new DoozR_Exception('Set URL ($this->url) first.');

        }

        $pattern = explode('/', trim($pattern));
        $url     = explode('/', $this->url);

        array_shift($pattern);
        array_shift($url);

        $result = array();
        $matrix = array();

        foreach ($pattern as $key => $partial) {
            $variable = preg_match('/{{(.*)}}/i', $partial, $result);
            if ($variable === 1 && isset($url[$key])) {
                //$$result[1] = $url[$key];
                $matrix[] = $url[$key];
            }
        }

        if ($callback !== null) {
            return call_user_func_array($callback, $matrix);

        } else {
            return $matrix;

        }
    }

    /**
     * Set a list (array) of properties.
     *
     * @param array $config The properties to set as array (key => value)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_Api
     * @access public
     */
    public function set(array $config)
    {
        // iterate config and set properties
        foreach ($config as $property => $value) {
            $this->{$property} = $value;
        }

        return $this;
    }
}
