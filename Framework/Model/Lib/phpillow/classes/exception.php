<?php
/**
 * phpillow CouchDB backend
 *
 * This file is part of phpillow.
 *
 * phpillow is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * phpillow is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with phpillow; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Basic phpillow exception
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
abstract class phpillowException extends Exception
{
    /**
     * Exception message with optional placeholders
     *
     * @var mixed
     */
    protected $rawMessage;

    /**
     * Array with placeholder replacers.
     *
     * @var array
     */
    protected $properties;

    /**
     * Construct exception message
     *
     * Construct exception message of a string with placeholders and the
     * properties array, where the properties are the values, which will
     * replace the placeholders when the exception is echo'd.
     *
     * This is done to make it possible to echo translated error messages.
     *
     * @param string $message
     * @param array $properties
     * @return void
     */
    public function __construct( $message, array $properties )
    {
        $this->rawMessage = $message;
        $this->properties = $properties;

        parent::__construct( $this->buildMessage( $message, $properties ) );
    }

    /**
     * Build exception message
     *
     * Replace all placeholders in exception message. The exception to do so
     * has been "borrowed" from ezcTranslations, as this will used for the
     * translation, so that we are using the exact same replacement strategy.
     *
     * @param string $message
     * @param array $properties
     * @return string
     */
    protected function buildMessage( $message, array $properties )
    {
        return preg_replace( '(%(([A-Za-z][a-z_]*[a-z])|[1-9]))e', '$properties["\\1"]', $message );
    }

    /**
     * Get message
     *
     * Get raw exception message without replaced placeholders
     *
     * @return string
     */
    public function getText()
    {
        return $this->rawMessage;
    }

    /**
     * Get properties
     *
     * Get text properties containing the values, which should replace the
     * placeholders in the message.
     *
     * @return array
     */
    public function getTextValues()
    {
        return $this->properties;
    }
}

/**
 * Runtime exception for really unexpected failures.
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowRuntimeException extends phpillowException
{
    /**
     * Construct runtime exception from exception message.
     *
     * @param string $message
     * @return void
     */
    public function __construct( $message )
    {
        parent::__construct(
            'Runtime exception: %message',
            array(
                'message' => $message,
            )
        );
    }
}

/**
 * Exception thrown, when connection could not be established or
 * configured.
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowConnectionException extends phpillowException
{
}

/**
 * Exception thrown, when trying to set an unknown option
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowOptionException extends phpillowException
{
    /**
     * Create exception from option name
     *
     * @param string $option
     * @return void
     */
    public function __construct( $option )
    {
        parent::__construct(
            "Unknown option '%option'.",
            array(
                'option' => $option,
            )
        );
    }
}

/**
 * Exception thrown, when no database has been configured.
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowNoDatabaseException extends phpillowException
{
    /**
     * Create exception
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(
            "No database has been configured.",
            array(
            )
        );
    }
}

/**
 * Exception thrown, when a request could not be build out of the given
 * parameters
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowInvalidRequestException extends phpillowException
{
}

/**
 * Exception thrown, when a property requested from an response object is
 * not available.
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowNoSuchPropertyException extends phpillowException
{
    /**
     * Create exception from property name
     *
     * @param string $property
     * @return void
     */
    public function __construct( $property )
    {
        parent::__construct(
            "Property '%property' is not available.",
            array(
                'property' => $property,
            )
        );
    }
}

/**
 * Exception thrown, when a document property could not be validated by the
 * validator.
 *
 * The exception contains an identifier for the error type, if the error should
 * be presented to the user.
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowValidationException extends phpillowException
{
}

/**
 * Exception thrown, when a document property could not be validated by the
 * or-validator.
 *
 * The exception contains an identifier for the error type, if the error should
 * be presented to the user.
 *
 * This exception contains the exceptions of the validators that were used 
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowOrValidationException extends phpillowValidationException
{
    /**
     * Contains the exceptions of the validators that were used
     *
     * @var array(string => exception)
     */
    public $validatorExceptions;
}


/**
 * Exception thrown if the server could not properly response a request.
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowResponseErrorException extends phpillowException
{
    /**
     * Actual parsed server response
     *
     * @var StdClass
     */
    protected $response;

    /**
     * Construct exception out of given response
     *
     * @param int $status
     * @param array $response
     * @return phpillowResponseErrorException
     */
    public function __construct( $status, $response )
    {
        $this->response = $response;

        parent::__construct(
            "Error (%status) in request: %error (%reason).",
            array(
                'status'    => $status,
                'error'     => $response !== null ? $response['error'] : 'Unknown',
                'reason'    => $response !== null ? $response['reason'] : 'Unknown',
            )
        );
    }

    /**
     * Return response
     *
     * Return response to check the actual response which cause the error,
     * or receive details about the server error.
     *
     * @return StdClass
     */
    public function getResponse()
    {
        return $this->response;
    }
}

/**
 * Exception thrown if the server could not find a requested document.
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowResponseNotFoundErrorException extends phpillowResponseErrorException
{
    /**
     * Construct parent from response
     *
     * @param array $response
     * @return void
     */
    public function __construct( $response )
    {
        parent::__construct( 404, $response );
    }
}

/**
 * Exception thrown if the server detected a conflict while processing a
 * request.
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowResponseConflictErrorException extends phpillowResponseErrorException
{
    /**
     * Construct parent from response
     *
     * @param array $response
     * @return void
     */
    public function __construct( $response )
    {
        parent::__construct( 409, $response );
    }
}

/**
 * Exception thrown if the parsing of a multipart/mixed document failed.
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class phpillowMultipartParserException extends phpillowException
{
    /**
     * Construct parent from message
     *
     * @param string $message
     * @return void
     */
    public function __construct( $message )
    {
        parent::__construct( $message, array() );
    }
}

