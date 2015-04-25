<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Http
 *
 * Http.php - Class for HTTP operations / status codes / and many more
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
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
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Http
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class.php';

/**
 * Doozr - Http
 *
 * Http.php - Class for HTTP operations / status codes / and many more
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Http
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @link       http://en.wikipedia.org/wiki/HTTP-Statuscode
 */
class Doozr_Http extends Doozr_Base_Class
{
    /**
     * The request method aka verb GET.
     *
     * @var string
     * @access public
     * @const
     * @see http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_GET = 'GET';

    /**
     * The request method aka verb PUT.
     *
     * @var string
     * @access public
     * @const
     * @see http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_PUT = 'PUT';

    /**
     * The request method aka verb POST.
     *
     * @var string
     * @access public
     * @const
     * @see http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_POST = 'POST';

    /**
     * The request method aka verb HEAD.
     *
     * @var string
     * @access public
     * @const
     * @see http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_HEAD = 'HEAD';

    /**
     * The request method aka verb OPTIONS.
     *
     * @var string
     * @access public
     * @const
     * @see http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_OPTIONS = 'OPTIONS';

    /**
     * The request method aka verb DELETE.
     *
     * @var string
     * @access public
     * @const
     * @see http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_DELETE = 'DELETE';

    /**
     * The request method aka verb TRACE.
     *
     * @var string
     * @access public
     * @const
     * @see http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_TRACE = 'TRACE';

    /**
     * The request method aka verb CONNECT.
     *
     * @var string
     * @access public
     * @const
     * @see http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_CONNECT = 'CONNECT';



    const STATUS_100 = 'Continue';
    const STATUS_101 = 'Switching Protocols';

    const STATUS_200 = 'OK';
    const STATUS_201 = 'Created';
    const STATUS_202 = 'Accepted';
    const STATUS_203 = 'Non-Authoritative Information';
    const STATUS_204 = 'No Content';
    const STATUS_205 = 'Reset Content';
    const STATUS_206 = 'Partial Content';

    const STATUS_300 = 'Multiple Choices';
    const STATUS_301 = 'Moved Permanently';
    const STATUS_302 = 'Found';
    const STATUS_303 = 'See Other';
    const STATUS_304 = 'Not Modified';
    const STATUS_305 = 'Use Proxy';
    const STATUS_307 = 'Temporary Redirect';

    // 400er
    const STATUS_400 = 'Bad Request';
    const STATUS_401 = 'Unauthorized';
    const STATUS_402 = 'Payment Required';
    const STATUS_403 = 'Forbidden';
    const STATUS_404 = 'Not Found';
    const STATUS_405 = 'Method Not Allowed';
    const STATUS_406 = 'Not Acceptable';
    const STATUS_407 = 'Proxy Authentication Required';
    const STATUS_408 = 'Request Time-out';
    const STATUS_409 = 'Conflict';
    const STATUS_410 = 'Gone';
    const STATUS_411 = 'Length Required';
    const STATUS_412 = 'Precondition Failed';
    const STATUS_413 = 'Request Entity Too Large';
    const STATUS_414 = 'Request-URI Too Large';
    const STATUS_415 = 'Unsupported Media Type';
    const STATUS_416 = 'Requested range not satisfiable';
    const STATUS_417 = 'Expectation Failed';
    const STATUS_418 = 'I\'m a teapot';
    const STATUS_420 = 'Policy Not Fulfilled';
    const STATUS_421 = 'There are too many connections from your internet address';
    const STATUS_422 = 'Unprocessable Entity';
    const STATUS_423 = 'Locked';
    const STATUS_424 = 'Failed Dependency';
    const STATUS_425 = 'Unordered Collection';
    const STATUS_426 = 'Upgrade Required';
    const STATUS_428 = 'Precondition Required';
    const STATUS_429 = 'Too Many Requests';
    const STATUS_431 = 'Request Header Fields Too Large';
    const STATUS_444 = 'No Response';
    const STATUS_449 = 'The request should be retried after doing the appropriate action';
    const STATUS_451 = 'Unavailable For Legal Reasons';

    // 500er
    const STATUS_500 = 'Internal Server Error';
    const STATUS_501 = 'Not Implemented';
    const STATUS_502 = 'Bad Gateway';
    const STATUS_503 = 'Service Unavailable';
    const STATUS_504 = 'Gateway Time-out';
    const STATUS_505 = 'HTTP Version not supported';

    const STATUS_CONTINUE                      = self::STATUS_100;
    const STATUS_SWITCHING_PROTOCOLS           = self::STATUS_101;
    const STATUS_OK                            = self::STATUS_200;
    const STATUS_CREATED                       = self::STATUS_201;
    const STATUS_ACCEPTED                      = self::STATUS_202;
    const STATUS_NON_AUTHORITATIVE_INFORMATION = self::STATUS_203;
    const STATUS_NO_CONTENT                    = self::STATUS_204;
    const STATUS_RESET_CONTENT                 = self::STATUS_205;
    const STATUS_PARTIAL_CONTENT               = self::STATUS_206;

}


/*
case 400:                   // GENERAL:               All x00er are general ;) error
case 401:                   // UNAUTHORIZED:          If a user is not loggedin for a resource access
case 403:                   // FORBIDDEN:             If user does not have right to access a resource
case 404:                   // NOT FOUND:             The resource accessed does not exist
case 405:                   // METHOD NOT ALLOWED:    For not allowed Verb
case 406:                   // NOT ACCEPTABLE         For missing argument + message
case 422:                   // UNPROCESSABLE ENTITIY: Error in arguments or while processing model
*/

