<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Http
 *
 * Http.php - Class for HTTP operations / status codes / and many more
 *
 * PHP versions 5.5
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
    /*------------------------------------------------------------------------------------------------------------------
    | REQUEST METHODS / VERBS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * The request method aka verb GET.
     *
     * @var string
     * @access public
     * @link http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_GET = 'GET';

    /**
     * The request method aka verb PUT.
     *
     * @var string
     * @access public
     * @link http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_PUT = 'PUT';

    /**
     * The request method aka verb POST.
     *
     * @var string
     * @access public
     * @link http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_POST = 'POST';

    /**
     * The request method aka verb HEAD.
     *
     * @var string
     * @access public
     * @link http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_HEAD = 'HEAD';

    /**
     * The request method aka verb OPTIONS.
     *
     * @var string
     * @access public
     * @link http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_OPTIONS = 'OPTIONS';

    /**
     * The request method aka verb DELETE.
     *
     * @var string
     * @access public
     * @link http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_DELETE = 'DELETE';

    /**
     * The request method aka verb TRACE.
     *
     * @var string
     * @access public
     * @link http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_TRACE = 'TRACE';

    /**
     * The request method aka verb CONNECT.
     *
     * @var string
     * @access public
     * @link http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
     */
    const REQUEST_METHOD_CONNECT = 'CONNECT';

    /*------------------------------------------------------------------------------------------------------------------
    | REASONPHRASE BY CODE
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Reason: Continue.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_100 = 'Continue';

    /**
     * Reason: Switching Protocols.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_101 = 'Switching Protocols';

    /**
     * Reason: Processing.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_102 = 'Processing';

    /**
     * Reason: OK.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_200 = 'OK';

    /**
     * Reason: Created.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_201 = 'Created';

    /**
     * Reason: Accepted.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_202 = 'Accepted';

    /**
     * Reason: Non-Authoritative Information.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_203 = 'Non-Authoritative Information';

    /**
     * Reason: No Content.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_204 = 'No Content';

    /**
     * Reason: Reset Content.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_205 = 'Reset Content';

    /**
     * Reason: Partial Content.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_206 = 'Partial Content';

    /**
     * Reason: Multi Status.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_207 = 'Multi Status';

    /**
     * Reason: Already Reported.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_208 = 'Already Reported';

    /**
     * Reason: IM Used.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_226 = 'IM Used';

    /**
     * Reason: Multiple Choices.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_300 = 'Multiple Choices';

    /**
     * Reason: Moved Permanently.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_301 = 'Moved Permanently';

    /**
     * Reason: Found.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_302 = 'Found';

    /**
     * Reason: See other.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_303 = 'See Other';

    /**
     * Reason: Not modified.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_304 = 'Not Modified';

    /**
     * Reason: Use Proxy.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_305 = 'Use Proxy';

    /**
     * Reason: (DEPRECATED) Switch Proxy.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_306 = 'Switch Proxy';

    /**
     * Reason: Temporary Redirect.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_307 = 'Temporary Redirect';

    /**
     * Reason: Permanent Redirect.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_308 = 'Permanent Redirect';

    /**
     * Reason: Bad Request.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_400 = 'Bad Request';

    /**
     * Reason: Unauthorized.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_401 = 'Unauthorized';

    /**
     * Reason: Payment Required.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_402 = 'Payment Required';

    /**
     * Reason: Forbidden.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_403 = 'Forbidden';

    /**
     * Reason: Not Found.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_404 = 'Not Found';

    /**
     * Reason: Method Not Allowed.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_405 = 'Method Not Allowed';

    /**
     * Reason: Not Acceptable.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_406 = 'Not Acceptable';

    /**
     * Reason: Proxy Authentication Required.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_407 = 'Proxy Authentication Required';

    /**
     * Reason: Request Time-out.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_408 = 'Request Time-out';

    /**
     * Reason: Conflict.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_409 = 'Conflict';

    /**
     * Reason: Gone.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_410 = 'Gone';

    /**
     * Reason: Length Required.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_411 = 'Length Required';

    /**
     * Reason: Precondition Failed.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_412 = 'Precondition Failed';

    /**
     * Reason: Request Entity Too Large.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_413 = 'Request Entity Too Large';

    /**
     * Reason: Request-URL Too Large.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_414 = 'Request-URL Too Large';

    /**
     * Reason: Unsupported Media Type.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_415 = 'Unsupported Media Type';

    /**
     * Reason: Requested range not satisfiable.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_416 = 'Requested range not satisfiable';

    /**
     * Reason: Expectation Failed.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_417 = 'Expectation Failed';

    /**
     * Reason: I'm a teapot.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_418 = 'I\'m a teapot';

    /**
     * Reason: Policy Not Fulfilled.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_420 = 'Policy Not Fulfilled';

    /**
     * Reason: There are too many connections from your internet address.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_421 = 'There are too many connections from your internet address';

    /**
     * Reason: Unprocessable Entity.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_422 = 'Unprocessable Entity';

    /**
     * Reason: Locked.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_423 = 'Locked';

    /**
     * Reason: Failed Dependency.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_424 = 'Failed Dependency';

    /**
     * Reason: Unordered Collection.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_425 = 'Unordered Collection';

    /**
     * Reason: Upgrade Required.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_426 = 'Upgrade Required';

    /**
     * Reason: Precondition Required.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_428 = 'Precondition Required';

    /**
     * Reason: Too Many Requests.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_429 = 'Too Many Requests';

    /**
     * Reason: Request Header Fields Too Large.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_431 = 'Request Header Fields Too Large';

    /**
     * Reason: No Response.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_444 = 'No Response';

    /**
     * Reason: The request should be retried after doing the appropriate action.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_449 = 'The request should be retried after doing the appropriate action';

    /**
     * Reason: Unavailable For Legal Reasons.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_451 = 'Unavailable For Legal Reasons';

    /**
     * Reason: Internal Server Error.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_500 = 'Internal Server Error';

    /**
     * Reason: Not Implemented.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_501 = 'Not Implemented';

    /**
     * Reason: Bad Gateway.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_502 = 'Bad Gateway';

    /**
     * Reason: Service Unavailable.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_503 = 'Service Unavailable';

    /**
     * Reason: Gateway Time-out.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_504 = 'Gateway Time-out';

    /**
     * Reason: HTTP Version not supported.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_505 = 'HTTP Version not supported';

    /**
     * Reason: Variant Also Negotiates.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_506 = 'Variant Also Negotiates';

    /**
     * Reason: Insufficient Storage.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_507 = 'Insufficient Storage';

    /**
     * Reason: Loop Detected.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_508 = 'Loop Detected';

    /**
     * Reason: Bandwidth Limit Exceeded.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_509 = 'Bandwidth Limit Exceeded';

    /**
     * Reason: Not Extended.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_510 = 'Not Extended';

    /*------------------------------------------------------------------------------------------------------------------
    | REASONPHRASE BY NAME
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Reasonphrase: Continue.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_CONTINUE = self::REASONPHRASE_100;

    /**
     * Reasonphrase: Switching Protocols.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_SWITCHING_PROTOCOLS = self::REASONPHRASE_101;

    /**
     * Reasonphrase: Processing.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_PROCESSING = self::REASONPHRASE_102;

    /**
     * Reasonphrase: OK.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_OK = self::REASONPHRASE_200;

    /**
     * Reasonphrase: Created.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_CREATED = self::REASONPHRASE_201;

    /**
     * Reasonphrase: Accepted.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_ACCEPTED = self::REASONPHRASE_202;

    /**
     * Reasonphrase: Non-Authoritative Information.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_NON_AUTHORITATIVE_INFORMATION = self::REASONPHRASE_203;

    /**
     * Reasonphrase: No Content.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_NO_CONTENT = self::REASONPHRASE_204;

    /**
     * Reasonphrase: Reset Content.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_RESET_CONTENT = self::REASONPHRASE_205;

    /**
     * Reasonphrase: Partial Content.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_PARTIAL_CONTENT = self::REASONPHRASE_206;

    /**
     * Reasonphrase: Multi Status.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_MULTI_STATUS = self::REASONPHRASE_207;

    /**
     * Reasonphrase: Already Reported.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_ALREADY_REPORTED = self::REASONPHRASE_208;

    /**
     * Reasonphrase: IM Used.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_IM_USED = self::REASONPHRASE_226;

    /**
     * Reasonphrase: Multiple Choices.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_MULTIPLE_CHOICES = self::REASONPHRASE_300;

    /**
     * Reasonphrase: Moved Permanently.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_MOVED_PERMANENTLY = self::REASONPHRASE_301;

    /**
     * Reasonphrase: Found.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_FOUND = self::REASONPHRASE_302;

    /**
     * Reasonphrase: See Other.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_SEE_OTHER = self::REASONPHRASE_303;

    /**
     * Reasonphrase: Not Modified.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_NOT_MODIFIED = self::REASONPHRASE_304;

    /**
     * Reasonphrase: Use Proxy.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_USE_PROXY = self::REASONPHRASE_305;

    /**
     * Reasonphrase: Switch Proxy.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_SWITCH_PROXY = self::REASONPHRASE_306;

    /**
     * Reasonphrase: Temporary Redirect.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_TEMPORARY_REDIRECT = self::REASONPHRASE_307;

    /**
     * Reasonphrase: Permanent Redirect.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_PERMANENT_REDIRECT = self::REASONPHRASE_308;

    /**
     * Reasonphrase: Bad Request.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_BAD_REQUEST = self::REASONPHRASE_400;

    /**
     * Reasonphrase: Unauthorized.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_UNAUTHORIZED = self::REASONPHRASE_401;

    /**
     * Reasonphrase: Payment Required.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_PAYMENT_REQUIRED = self::REASONPHRASE_402;

    /**
     * Reasonphrase: Forbidden.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_FORBIDDEN = self::REASONPHRASE_403;

    /**
     * Reasonphrase: Not Found.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_NOT_FOUND = self::REASONPHRASE_404;

    /**
     * Reasonphrase: Method Not Allowed.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_METHOD_NOT_ALLOWED = self::REASONPHRASE_405;

    /**
     * Reasonphrase: Not Acceptable.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_NOT_ACCEPTABLE = self::REASONPHRASE_406;

    /**
     * Reasonphrase: Proxy Authentication Required.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_PROXY_AUTHENTICATION_REQUIRED = self::REASONPHRASE_407;

    /**
     * Reasonphrase: Request Time Out.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_REQUEST_TIME_OUT = self::REASONPHRASE_408;

    /**
     * Reasonphrase: Conflict.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_CONFLICT = self::REASONPHRASE_409;

    /**
     * Reasonphrase: Gone.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_GONE = self::REASONPHRASE_410;

    /**
     * Reasonphrase: Length Required.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_LENGTH_REQUIRED = self::REASONPHRASE_411;

    /**
     * Reasonphrase: Precondition Failed.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_PRECONDITION_FAILED = self::REASONPHRASE_412;

    /**
     * Reasonphrase: Request Entitiy Too Large.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_REQUEST_ENTITY_TOO_LARGE = self::REASONPHRASE_413;

    /**
     * Reasonphrase: Request-URL Too Long.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_REQUEST_URL_TOO_LONG = self::REASONPHRASE_414;

    /**
     * Reasonphrase: Unsupported Media Type.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_UNSUPPORTED_MEDIA_TYPE = self::REASONPHRASE_415;

    /**
     * Reasonphrase: Requested Range Not Satisfiable.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_REQUESTED_RANGE_NOT_SATISFIABLE = self::REASONPHRASE_416;

    /**
     * Reasonphrase: Expectation Failed.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_EXPECTATION_FAILED = self::REASONPHRASE_417;

    /**
     * Reasonphrase: I'm A Teapot.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_IM_A_TEAPOT = self::REASONPHRASE_418;

    /**
     * Reasonphrase: Policy Not Fulfilled.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_POLICY_NOT_FULFILLED = self::REASONPHRASE_420;

    /**
     * Reasonphrase: There Are Too Many Connections From Your Internet Address.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_THERE_ARE_TOO_MANY_CONNECTIONS_FROM_YOUR_INTERNET_ADDRESS = self::REASONPHRASE_421;

    /**
     * Reasonphrase: Unprocessable Entity.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_UNPROCESSABLE_ENTITY = self::REASONPHRASE_422;

    /**
     * Reasonphrase: Locked.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_LOCKED = self::REASONPHRASE_423;

    /**
     * Reasonphrase: Failed Dependency.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_FAILED_DEPENDENCY = self::REASONPHRASE_424;

    /**
     * Reasonphrase: Unordered Collection.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_UNORDERED_COLLECTION = self::REASONPHRASE_425;

    /**
     * Reasonphrase: Upgrade Required.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_UPGRADE_REQUIRED = self::REASONPHRASE_426;

    /**
     * Reasonphrase: Precondition Required.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_PRECONDITION_REQUIRED = self::REASONPHRASE_428;

    /**
     * Reasonphrase: Too Many Requests.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_TOO_MANY_REQUESTS = self::REASONPHRASE_429;

    /**
     * Reasonphrase: Request Header Fields Too Large.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_REQUEST_HEADER_FIELDS_TOO_LARGE = self::REASONPHRASE_431;

    /**
     * Reasonphrase: No Response.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_NO_RESPONSE = self::REASONPHRASE_444;

    /**
     * Reasonphrase: The Request Should Be Retried After Doing The Appropriate Action.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_THE_REQUEST_SHOULD_BE_RETRIED_AFTER_DOING_THE_APPROPRIATE_ACTION = self::REASONPHRASE_449;

    /**
     * Reasonphrase: Unavailable For Legal Reasons.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_UNAVAILABLE_FOR_LEGAL_REASONS = self::REASONPHRASE_451;

    /**
     * Reasonphrase: Internal Server Error.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_INTERNAL_SERVER_ERROR = self::REASONPHRASE_500;

    /**
     * Reasonphrase: Not Implemented.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_NOT_IMPLEMENTED = self::REASONPHRASE_501;

    /**
     * Reasonphrase: Bad Gateway.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_BAD_GATEWAY = self::REASONPHRASE_502;

    /**
     * Reasonphrase: Service Unavailable.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_SERVICE_UNAVAILABLE = self::REASONPHRASE_503;

    /**
     * Reasonphrase: Gateway Time Out.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_GATEWAY_TIME_OUT = self::REASONPHRASE_504;

    /**
     * Reasonphrase: HTTP Version Not Supported.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_HTTP_VERSION_NOT_SUPPORTED = self::REASONPHRASE_505;

    /**
     * Reasonphrase: Variant Also Negotiates.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_VARIANT_ALSO_NEGOTIATES = self::REASONPHRASE_506;

    /**
     * Reasonphrase: Insufficient Storage.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_INSUFFICIENT_STORAGE = self::REASONPHRASE_507;

    /**
     * Reasonphrase: Loop Detected.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_LOOP_DETECTED = self::REASONPHRASE_508;

    /**
     * Reasonphrase: Bandwidth Limit Exceeded.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_BANDWIDTH_LIMIT_EXCEEDED = self::REASONPHRASE_509;

    /**
     * Reasonphrase: Not Extended.
     *
     * @var string
     * @access public
     */
    const REASONPHRASE_NOT_EXTENDED = self::REASONPHRASE_510;

    /*------------------------------------------------------------------------------------------------------------------
    | STATUSCODE BY CODE
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * HTTP Status 100
     * Continue
     *
     * @var int
     * @access public
     */
    const STATUS_100 = 100;

    /**
     * HTTP Status 101
     * Switching Protocols
     *
     * @var int
     * @access public
     */
    const STATUS_101 = 101;

    /**
     * HTTP Status 102
     * Switching Protocols
     *
     * @var int
     * @access public
     */
    const STATUS_102 = 102;

    /**
     * HTTP Status 200
     * OK
     *
     * @var int
     * @access public
     */
    const STATUS_200 = 200;

    /**
     * HTTP Status 201
     * Created
     *
     * @var int
     * @access public
     */
    const STATUS_201 = 201;

    /**
     * HTTP Status 202
     * Accepted
     *
     * @var int
     * @access public
     */
    const STATUS_202 = 202;

    /**
     * HTTP Status 203
     * Non-Authoritative Information
     *
     * @var int
     * @access public
     */
    const STATUS_203 = 203;

    /**
     * HTTP Status 204
     * No Content
     *
     * @var int
     * @access public
     */
    const STATUS_204 = 204;

    /**
     * HTTP Status 205
     * Reset Content
     *
     * @var int
     * @access public
     */
    const STATUS_205 = 205;

    /**
     * HTTP Status 206
     * Partial Content
     *
     * @var int
     * @access public
     */
    const STATUS_206 = 206;

    /**
     * HTTP Status 207
     * Multi Status
     *
     * @var int
     * @access public
     */
    const STATUS_207 = 207;

    /**
     * HTTP Status 208
     * Already Reported
     *
     * @var int
     * @access public
     */
    const STATUS_208 = 208;

    /**
     * HTTP Status 226
     * IM Used
     *
     * @var int
     * @access public
     */
    const STATUS_226 = 226;

    /**
     * HTTP Status
     *
     * @var int
     * @access public
     */
    const STATUS_300 = 300;

    /**
     * HTTP Status
     *
     * @var int
     * @access public
     */
    const STATUS_301 = 301;

    /**
     * HTTP Status
     *
     * @var int
     * @access public
     */
    const STATUS_302 = 302;

    /**
     * HTTP Status
     *
     * @var int
     * @access public
     */
    const STATUS_303 = 303;

    /**
     * HTTP Status
     *
     * @var int
     * @access public
     */
    const STATUS_304 = 304;

    /**
     * Reason: Use Proxy.
     *
     * @var int
     * @access public
     */
    const STATUS_305 = 305;

    /**
     * HTTP Status
     *
     * @var int
     * @access public
     */
    const STATUS_306 = 306;

    /**
     * HTTP Status
     *
     * @var int
     * @access public
     */
    const STATUS_307 = 307;

    /**
     * HTTP Status 308
     *
     * @var int
     * @access public
     */
    const STATUS_308 = 308;

    /**
     * HTTP Status 400
     * GENERAL: All x00er are general ;) error
     *
     * @var int
     * @access public
     */
    const STATUS_400 = 400;

    /**
     * HTTP Status 401
     * UNAUTHORIZED: If a user is not logged in for a resource access
     *
     * @var int
     * @access public
     */
    const STATUS_401 = 401;

    /**
     * HTTP Status 402
     * Payment Required
     *
     * @var int
     * @access public
     */
    const STATUS_402 = 402;

    /**
     * HTTP Status 403
     * FORBIDDEN: If user does not have right to access a resource
     *
     * @var int
     * @access public
     */
    const STATUS_403 = 403;

    /**
     * HTTP Status 404
     * NOT FOUND: The resource accessed does not exist
     *
     * @var int
     * @access public
     */
    const STATUS_404 = 404;

    /**
     * HTTP Status 405
     * METHOD NOT ALLOWED: For not allowed Verb
     *
     * @var int
     * @access public
     */
    const STATUS_405 = 405;

    /**
     * HTTP Status 406
     * Not Acceptable
     *
     * @var int
     * @access public
     */
    const STATUS_406 = 406;

    /**
     * HTTP Status 407
     * Proxy Authentication Required
     *
     * @var int
     * @access public
     */
    const STATUS_407 = 407;

    /**
     * HTTP Status 408
     * Request Time-out
     *
     * @var int
     * @access public
     */
    const STATUS_408 = 408;

    /**
     * HTTP Status 409
     * Conflict
     *
     * @var int
     * @access public
     */
    const STATUS_409 = 409;

    /**
     * HTTP Status 410
     * Gone
     *
     * @var int
     * @access public
     */
    const STATUS_410 = 410;

    /**
     * HTTP Status 411
     * Length Required
     *
     * @var int
     * @access public
     */
    const STATUS_411 = 411;

    /**
     * HTTP Status 412
     * Precondition Failed
     *
     * @var int
     * @access public
     */
    const STATUS_412 = 412;

    /**
     * HTTP Status 413
     * Request Entity Too Large
     *
     * @var int
     * @access public
     */
    const STATUS_413 = 413;

    /**
     * HTTP Status 414
     * Request-URL Too Large
     *
     * @var int
     * @access public
     */
    const STATUS_414 = 414;

    /**
     * HTTP Status 415
     * Unsupported Media Type
     *
     * @var int
     * @access public
     */
    const STATUS_415 = 415;

    /**
     * HTTP Status 416
     * Requested range not satisfiable
     *
     * @var int
     * @access public
     */
    const STATUS_416 = 416;

    /**
     * HTTP Status 417
     * Expectation Failed
     *
     * @var int
     * @access public
     */
    const STATUS_417 = 417;

    /**
     * HTTP Status 418
     * I'm a teapot
     *
     * @var int
     * @access public
     */
    const STATUS_418 = 418;

    /**
     * HTTP Status 420
     * Policy Not Fulfilled
     *
     * @var int
     * @access public
     */
    const STATUS_420 = 420;

    /**
     * HTTP Status 421
     * There are too many connections from your internet address
     *
     * @var int
     * @access public
     */
    const STATUS_421 = 421;

    /**
     * HTTP Status 422
     * Unprocessable entity
     *
     * @var int
     * @access public
     */
    const STATUS_422 = 422;

    /**
     * HTTP Status 423
     * Locked
     *
     * @var int
     * @access public
     */
    const STATUS_423 = 423;

    /**
     * HTTP Status 424
     * Failed Dependency
     *
     * @var int
     * @access public
     */
    const STATUS_424 = 424;

    /**
     * HTTP Status 425
     * Unordered Collection
     *
     * @var int
     * @access public
     */
    const STATUS_425 = 425;

    /**
     * HTTP Status 426
     * Upgrade Required
     *
     * @var int
     * @access public
     */
    const STATUS_426 = 426;

    /**
     * HTTP Status 428
     * Precondition Required
     *
     * @var int
     * @access public
     */
    const STATUS_428 = 428;

    /**
     * HTTP Status 429
     * Too Many Requests
     *
     * @var int
     * @access public
     */
    const STATUS_429 = 429;

    /**
     * HTTP Status 431
     * Request Header Fields Too Large
     *
     * @var int
     * @access public
     */
    const STATUS_431 = 431;

    /**
     * HTTP Status 444
     * No Response
     *
     * @var int
     * @access public
     */
    const STATUS_444 = 444;

    /**
     * HTTP Status 449
     * The request should be retried after doing the appropriate action
     *
     * @var int
     * @access public
     */
    const STATUS_449 = 449;

    /**
     * HTTP Status 451
     * Unavailable For Legal Reasons
     *
     * @var int
     * @access public
     */
    const STATUS_451 = 451;

    /**
     * HTTP Status 500
     *
     * @var int
     * @access public
     */
    const STATUS_500 = 500;

    /**
     * HTTP Status 501
     *
     * @var int
     * @access public
     */
    const STATUS_501 = 501;

    /**
     * HTTP Status 502
     *
     * @var int
     * @access public
     */
    const STATUS_502 = 502;

    /**
     * HTTP Status 503
     *
     * @var int
     * @access public
     */
    const STATUS_503 = 503;

    /**
     * HTTP Status 504
     *
     * @var int
     * @access public
     */
    const STATUS_504 = 504;

    /**
     * HTTP Status 505
     *
     * @var int
     * @access public
     */
    const STATUS_505 = 505;

    /**
     * HTTP Status 506
     *
     * @var int
     * @access public
     */
    const STATUS_506 = 506;

    /**
     * HTTP Status 507
     *
     * @var int
     * @access public
     */
    const STATUS_507 = 507;

    /**
     * HTTP Status 508
     *
     * @var int
     * @access public
     */
    const STATUS_508 = 508;

    /**
     * HTTP Status 509
     *
     * @var int
     * @access public
     */
    const STATUS_509 = 509;

    /**
     * HTTP Status 510
     *
     * @var int
     * @access public
     */
    const STATUS_510 = 510;

    /**
     * HTTP Version 1.1
     *
     * @var string
     * @access public
     */
    const VERSION_1_1 = '1.1';

    /**
     * Sends no cache headers to ensure that a response wont get cached on its way to the client.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if header could be send, otherwise FALSE
     * @access public
     * @static
     */
    public static function sendNoCacheHeaders()
    {
        if (false === headers_sent()) {
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');

            return true;
        }

        return false;
    }

    /**
     * Returns the current URL used for request.
     *
     * @param bool $filter TRUE to filter Doozr's front-controllers from URL, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if header could be send, otherwise FALSE
     * @access public
     * @link http://stackoverflow.com/questions/176264/what-is-the-difference-between-a-uri-a-url-and-a-urn
     * @static
     */
    public static function getUrl($filter = true)
    {
        static $uri = null;

        if (null === $uri) {
            $uri = (true === is_ssl()) ? 'https://' : 'http://';
            $uri .= $_SERVER['SERVER_NAME'];

            if ($_SERVER['SERVER_PORT'] !== '80' && $_SERVER['SERVER_PORT'] !== '443') {
                $uri .= ':'.$_SERVER['SERVER_PORT'];
            }

            $uri .= $_SERVER['REQUEST_URI'];

            if (true === $filter) {
                $uri = str_replace('/app.php', '', $uri);
                $uri = str_replace('/app_dev.php', '', $uri);
                $uri = str_replace('/app_httpd.php', '', $uri);
            }
        }

        return $uri;
    }

    /**
     * Returns the protocol (HTTP[S]) used for connecting to Doozr.
     *
     * @param bool $plain TRUE to retrieve the protocol without dot + slashes, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The protocol used while accessing a resource
     * @access public
     * @static
     */
    public static function getProtocol($plain = false)
    {
        static $protocol = null;

        if (null === $protocol) {
            if (true === is_ssl()) {
                $protocol = 'https';
            } else {
                $protocol = 'http';
            }
        }

        return $protocol . (false === $plain) ? '://' : '';
    }
}
