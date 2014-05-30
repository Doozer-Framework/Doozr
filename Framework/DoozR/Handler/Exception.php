<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Handler - Exception
 *
 * Exception.php - Exception-Handler of the DoozR-Framework which overrides
 * the PHP default exception-handler (handling)
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
 * @package    DoozR_Handler
 * @subpackage DoozR_Handler_Exception
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class.php';

// DoozR constants for the three main exception-types (codes like PHP error-types)
define('E_USER_EXCEPTION', 23);
define('E_USER_CORE_EXCEPTION', 235);
define('E_USER_CORE_FATAL_EXCEPTION', 23523);

/**
 * DoozR - Handler - Exception
 *
 * Exception-Handler of the DoozR-Framework which overrides
 * the PHP default exception-handler (handling)
 *
 * @category   DoozR
 * @package    DoozR_Handler
 * @subpackage DoozR_Handler_Exception
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
final class DoozR_Handler_Exception extends DoozR_Base_Class
{
    /**
     * The class as identifier to be able to extract
     * the real exception/error from a double-packed exception
     *
     * @var string
     * @access const
     */
    const DOOZR_ERROR_EXCEPTION = 'DoozR_Error_Exception';

    /**
     * The class of DoozR's Error Handler
     *
     * @var string
     * @access const
     */
    const DOOZR_ERROR_HANDLER = 'DoozR_Handler_Error';

    /**
     * The name of method which handles an error within error
     * handler.
     *
     * @var string
     * @access const
     */
    const DOOZR_ERROR_METHOD = 'handle';

    private static $_registry;

    /**
     * Template/Snippets used for generating exception screen
     *
     * @var array
     * @access private
     */
    private static $_templates = array(
        'page'          => '<!DOCTYPE html><html lang="de"><head><title>{{title}}</title><meta http-equiv="content-type" content="text/html; charset=utf-8" /></head><body style="background-color: #333; margin: 0;">{{exception-box}}{{request}}</body></html>',
        'exception-box' => '<table style="border:0; font-family: \'Andele Mono\', sans-serif; font-size: 14px; color: #666; width: 960px;" align="left"><tr><td><h2 style="font-weight:bold;">{{title}}</h2></td><td align="right">{{memory-bar}}</td></tr>

        		<tr>
			<td colspan="2" style="color: #fff; font-size: 18px;"><i>{{message}}</i></td>
		</tr>
		<tr>
			<td colspan="2" style="border-bottom: 1px solid #fff; height: 9px;">&nbsp;</td>
		</tr>
        <tr>
			<td style="color: #fff; font-size: 14px;"><span style="color: #ccc; font-size: 10px;">File</span> <span style="color: #eee;font-family:Georgia;">{{file}}</span></td>
			<td style="color: #fff; font-size: 14px;" align="right"><span style="color: #ccc; font-size: 10px;">Line</span> <span style="font-weight: bold; color: #cc6600;font-family:Georgia;">{{line}}</span></td>
		</tr>
		<tr>
			<td style="color: #fff; font-size: 14px;"><span style="color: #ccc; font-size: 10px;">Method</span> <span style="font-weight: bold; color: #ff00ff;font-family:Georgia;">{{function}}</span></td>
            <td style="color: #fff; font-size: 14px;" align="right"><span style="color: #ccc; font-size: 10px;">Class</span> <span style="font-weight: bold; color: #009999;font-family:Georgia;">{{class}}</span></td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" style="color: #fff; font-size: 12px;">
			<div style="width: 942px; background-color: #555; border: 0;padding: 9px; overflow: auto;"><i>Arguments passed to <b>{{function}}</b></i><br /><br />
                {{argument-table}}
			</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="height: 30px;">&nbsp;</td>
		</tr>
        {{callflow}}
        </table>',
        'arguments' => '
            <tr>
				<td align="right" style="color: #00aa97; border-top: 1px solid  #eee; height: 22px; background-color: #333;">{{argument-number}}</td>
				<td align="right" style="color: #cc0000;border-top: 1px solid  #eee; background-color: #333;">{{argument-datatype}}</td>
				<td align="right" style="color: #009900;border-top: 1px solid  #eee; background-color: #333;">{{argument-size-bytes}} byte(s)</td>
				<td align="right" style="color: #ff6600;border-top: 1px solid  #eee; background-color: #333;">{{argument-name}}</td>
				<td align="right" style="color: #cccc33;border-top: 1px solid  #eee; background-color: #333;">{{argument-value}}</td>
			</tr>
        ',
        'argument-table' => '
			<table style="border:0; width: 100%;" cellpadding="0" cellspacing="0" align="left">
			<tr>
				<th align="right">#</th>
				<th align="right">type</th>
				<th align="right">size (h)</th>
				<th align="right">name</th>
				<th align="right">value</th>
			</tr>
            {{arguments}}
			</table>
        ',
        'callflow' => '
            {{callflow-elements}}
        ',
        'callflow-element' => '
            <tr><td colspan="2">{{element}}</td></tr>
        ',
        'request' => '
            <table style="border:0; font-family: \'Andele Mono\', sans-serif; font-size: 14px; color: #666; width: 960px;" align="left">
            <tr><td colspan="2"><h2>→ Request (Method: {{request-type}})</h2>
            <table style="border:0; width: 100%;" cellpadding="0" cellspacing="0" align="left">
            <tr>
                <td colspan="2" style="color: #fff; font-size: 12px;">
                <div style="width: 942px; background-color: #555; border: 0;padding: 9px; overflow: auto;"><i>Request data</i><br /><br />
                    {{request-elements}}
                </div>
                </td>
            </tr>
            </table>
            </table>
        ',
        'request-element' => '
            <tr>
            <td align="right" style="color: #00aa97; border-top: 1px solid  #eee; height: 22px; background-color: #333; font-size:12px;">
                {{request-key}}
            </td>
            <td align="right" style="color: #cccc33; border-top: 1px solid  #eee; height: 22px; background-color: #333; font-size:12px;">
                {{request-value}}
            </td>
        ',
        'memory-bar'    => '
            <div style="border:1px solid #fff; width: 100px; height: 20px; background-color:#ccc;font-size: 12px;color:#111;">
			<div style="background-color:#99ff99;width: 18px; float:left; height: 20px;">&nbsp;</div>
			<div style="float:right;padding-top: 3px;">{{usage-megabyte}}&nbsp;/&nbsp;{{max-megabyte}}</div><div>'
    );


    /**
     * Replacement for PHP's default internal exception handler. All Exceptions are dispatched
     * to this method - we decide here what to do with it. We need this hook to stay informed
     * about DoozR's state and to pipe the Exceptions to attached Logger-Subsystem.
     *
     * @param object $exception The thrown and uncaught exception object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean Always TRUE
     * @access public
     * @static
     */
    public static function handle(Exception $exception)
    {
        self::$_registry = DoozR_Registry::getInstance();

        $requestAsString = '<n.a.>';
        $requestMethod   = '<n.a.>';

        if (self::$_registry->front !== null) {
            $requestAsString = self::$_registry->front->getRequest()->getRequestAsString();
            $requestMethod   = self::$_registry->front->getRequest()->getMethod();
        }

        $headers = array(
            'REQUEST-URI' => $_SERVER['REQUEST_URI'],
            'REQUEST-DATA' => $requestAsString
        );

        // iterate over $_SERVER to parse header from there
        foreach ($_SERVER as $header => $value) {
            if (preg_match('/HTTP_(.+)/i', $header, $headerParsed)) {
                $headers[$headerParsed[1]] = $value;
            }
        }

        $headers = self::convertArrayOfPhpTypesToStrings($headers);

        foreach ($headers as $key => $value) {
            $data = array(
                'request-key'   => $key,
                'request-value' => $value
            );

            $collection[] = self::parseTemplate(self::$_templates['request-element'], $data);
        }

        $data = array(
            'request-type'     => $requestMethod,
            'request-elements' => implode('', $collection)
        );

        $request = self::parseTemplate(self::$_templates['request'], $data);


        // extract unpack real exception if required
        $exception = self::unpackException($exception);

        // put the type into the exception object
        $exception = self::enrichException($exception);

        if (isset($exception->arguments)) {
            // get HTML of arguments
            $argumentsHtml = self::getArgumentHtml($exception->arguments, $exception->signature);

        } else {
            $argumentsHtml = '';
        }

        $trace = $exception->getTrace();

        // get callflow HTML
        $callflowHtml = self::getCallflowHtml($trace);

        // combine the HTML from callflow with exception box html
        $data = array(
            'message'        => $exception->getMessage(),
            'title'          => '¤ '.$exception->type,
            'class'          => (isset($exception->class) && $exception->class != '') ? $exception->class : '-&nbsp;',
            'function'       => (isset($exception->function)) ? ($exception->function.'('.(($exception->arguments) ? '...' : '').')') : '-&nbsp;',
            'file'           => $exception->getFile(),
            'line'           => $exception->getLine(),
            'memory-bar'     => self::getMemoryBarHtml(),
            'argument-table' => $argumentsHtml,
            'callflow'       => $callflowHtml
        );

        // parseTemplate(self::$_templates['exception-box'], $data);
        $html = self::getExceptionBoxHtml($data);

        // construct data for whole page HTML
        $data = array(
            'title'         => $exception->type,
            'exception-box' => $html,
            'request'       => $request
        );

        // print result
        echo self::parseTemplate(self::$_templates['page'], $data);
    }

    /**
     * Unpacks an exception from current exception. This mechanism is used
     * when an exception is forwarded from DoozR_Handler_Error.
     *
     * @param Exception $exception The current exception containing the forwarded one
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Exception The unpacked exception
     * @access protected
     * @static
     */
    protected static function unpackException(Exception $exception)
    {
        // check if unpacking required ...
        if (get_class($exception) === self::DOOZR_ERROR_EXCEPTION) {
            // ... do if type is error-exception
            $exception = $exception->getPrevious();
        }

        // return result
        return $exception;
    }

    /**
     * Enriches an exception with data from stacktrace.
     *
     * @param Exception $exception The current exception
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Exception The enriched exception
     * @access protected
     * @static
     */
    protected static function enrichException(Exception $exception)
    {
        // check for class of exception and construct custom type of this -> special case ...
        if (get_class($exception) === self::DOOZR_ERROR_EXCEPTION) {
            // ... error-exception is not set by default
            $exception->type = self::DOOZR_ERROR_EXCEPTION.' ('.$exception->type.')';

        } else {
            $exception->type = get_class($exception);

        }

        // extract details of execution from callflow and enrich the exception so it can be handled better
        $profiled = self::extractDetails($exception);

        // get accessible parts/properties
        $accessible = get_object_vars($exception);

        // prepare the details by adding not already retrieved values right here
        foreach ($profiled as $element => $value) {
            if (
                (!isset($exception->{$element}) || ($exception->{$element} === '' && $value != '')) &&
                in_array($element, $accessible)
            ) {
                $exception->{$element} = $value;
            }
        }

        // extract method from exception and ensure that both function and method is set
        if (isset($exception->method) && !isset($exception->function)) {
            $exception->function = &$exception->method;

        } elseif (isset($exception->function) && !isset($exception->method)) {
            $exception->method = &$exception->function;

        }

        // is some cases (small software) there is no function/method invoked
        if (isset($exception->function) && $exception->getFile() !== null) {
            // enrich with signature and arguments ...
            $exception->signature = self::extractSignature($exception->getFile(), $exception->function);

        } else {
            $exception->signature = array();

        }

        return $exception;
    }

    /**
     * Returns TRUE if current exception is a DoozR_Error_Handler (DoozR_Handler_Error)
     * Exception
     *
     * @param string $class    The class required for check
     * @param string $function The function required for check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return TRUE if current exception is a DoozR_Error_Handler Exception, otherwise FALSE
     * @access protected
     * @static
     */
    protected static function isDoozRErrorException($class, $function)
    {
        return ($class === self::DOOZR_ERROR_HANDLER && $function === self::DOOZR_ERROR_METHOD);
    }

    /**
     * Extracts the filename out of a passed callflow element
     *
     * @param array $element The callflow element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The extracted filename
     * @access protected
     * @static
     */
    protected static function getFilenameFromCallflowElement(array $element)
    {
        $class  = isset($element['class']) ? $element['class'] : null;
        $method = isset($element['function']) ? $element['function'] : null;

        if (self::isDoozRErrorException($class, $method)) {
            $filename = realpath_ext(
                realpath(
                    DOOZR_DOCUMENT_ROOT.str_replace('_', DIRECTORY_SEPARATOR, self::DOOZR_ERROR_HANDLER).'.php'
                )
            );
        } else {
            $filename = isset($element['file']) ? $element['file'] : null;
        }

        return $filename;
    }

    protected static function getLinenumberFromCallflowElement(array $element)
    {
        $class  = isset($element['class']) ? $element['class'] : null;
        $method = isset($element['function']) ? $element['function'] : null;

        if (self::isDoozRErrorException($class, $method)) {
            $linenumber = 106;
        } else {
            $linenumber = null;
        }

        return $linenumber;
    }

    /**
     * Extracts the functionname out of a passed callflow element
     *
     * @param array $element The callflow element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The extracted functionname
     * @access protected
     * @static
     */
    protected static function getFunctionFromCallflowElement(array $element)
    {
        $class  = isset($element['class']) ? $element['class'] : null;
        $method = isset($element['function']) ? $element['function'] : null;

        if (self::isDoozRErrorException($class, $method)) {
            $function = self::DOOZR_ERROR_METHOD;
        } else {
            $function = $element['function'];
        }

        return $function;
    }

    /**
     * Returns the parsed HTML template for exception box
     *
     * @param array $element The callflow element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The extracted functionname
     * @access protected
     * @static
     */
    protected static function getExceptionBoxHtml(array $templateData)
    {
        return self::parseTemplate(self::$_templates['exception-box'], $templateData);
    }

    /**
     * Returns the HTML Block (Table) filled with callflow
     * elements in a readable and so correct order.
     *
     * @param array $data The exception's trace/callflow
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The HTML of callflow
     * @access protected
     * @static
     */
    protected static function getCallflowHtml(array $data)
    {
        // get call stack elements and its count
        $callflowElements      = array_reverse($data);
        $callflowElementsHtml  = array();
        $countCallflowElements = count($callflowElements);

        // iterate callflow items - each item is a single entry in our result
        // each entry can have arguments and so on ...
        for ($i = 1; $i < $countCallflowElements; ++$i) {

            // get filename and function from callflow element (+ default)
            $filename   = self::getFilenameFromCallflowElement($callflowElements[$i]);
            $linenumber = self::getLinenumberFromCallflowElement($callflowElements[$i]);
            $function   = self::getFunctionFromCallflowElement($callflowElements[$i]);
            $classname  = self::extractClassFromArray($callflowElements[$i]);

            if ($filename !== null) {
                // get HTML for arguments
                $argumentsHtml = self::getArgumentHtml(
                    $callflowElements[$i]['args'],
                    self::extractSignature($filename, $function)
                );
            } else {
                $argumentsHtml = '';
            }

            // make call complex datatypes printable (to string)
            $callflowElements[$i] = self::convertArrayOfPhpTypesToStrings($callflowElements[$i]);

            // merge in the data required to fully complete the template
            $callflowElements[$i] = array_merge($callflowElements[$i], array(
                    'file'           => $filename,
                    'message'        => '',
                    'title'          => '↕ Callflow (e.g. executed elements)',
                    'class'          => $classname,
                    'function'       => $function.'('.(($callflowElements[$i]['args']) ? '...' : '').')',
                    'memory-bar'     => '',
                    'argument-table' => $argumentsHtml,
                    'callflow'       => ''
                )
            );

            if ($linenumber !== null) {
                $callflowElements[$i]['line'] = $linenumber;
            }

            // parse template and put result into collection
            $callflowElementsHtml[] = self::getExceptionBoxHtml($callflowElements[$i]);
        }

        // the data for the callflow template is filled here
        $data = array(
            'callflow-elements' => implode("\n", $callflowElementsHtml)
        );

        // return a parsed callflow template (HTML)
        return self::parseTemplate(self::$_templates['callflow'], $data);
    }

    /**
     * Returns the HTML of the argument template parsed with passed variables
     *
     * @param array $data The arguments extracted previously and passed here as array (key => value)
     * @param array $signature The signature of the function as previously extracted array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The HTML of the parsed template
     * @access protected
     * @static
     */
    protected static function getArgumentHtml(array $data, array $signature)
    {
        // assume empty result (HTML)
        $argumentsHtml  = '';
        $countArguments = count($data);

        // get arguments
        for ($i = 0; $i < $countArguments; ++$i) {

            // check if safe for recursion
            $safe = self::isSafe($data[$i]);

            $templateVars = array(
                'argument-number'     => $i+1,
                'argument-size-bytes' => ($safe) ? self::getSize($data[$i]) : '? *recursion*',
                'argument-datatype'   => gettype($data[$i]),
                'argument-name'       => (isset($signature['arguments'][$i])) ? $signature['arguments'][$i] : '???',
                'argument-value'      => ($safe) ? self::realValue($data[$i], gettype($data[$i])) : '?&nbsp;'
            );

            $argumentsHtml .= self::parseTemplate(self::$_templates['arguments'], $templateVars);
        }

        // fill data for argument table with inner content data
        $templateVars = array(
            'arguments' => $argumentsHtml
        );

        // return parsed content
        return self::parseTemplate(self::$_templates['argument-table'], $templateVars);
    }


    protected static function isSafe($variable)
    {
        $result = true;
        if (is_array($variable)) { // || is_object($variable)) {
            $result = !self::isRecursiveArray($variable);
        }

        return $result;
    }

    protected static function removeLastElementIfSame(array & $array, $reference) {
        if(end($array) === $reference) {
            unset($array[key($array)]);
        }
    }

    protected static function isRecursiveArrayIteration(array & $array, $reference) {
        $last_element   = end($array);
        if($reference === $last_element) {
            return true;
        }
        $array[]    = $reference;

        foreach($array as &$element) {
            if(is_array($element)) {
                if(self::isRecursiveArrayIteration($element, $reference)) {
                    self::removeLastElementIfSame($array, $reference);
                    return true;
                }
            }
        }

        self::removeLastElementIfSame($array, $reference);

        return false;
    }


    protected static function isRecursiveArray(array $array) {
        $some_reference = new stdclass();
        return self::isRecursiveArrayIteration($array, $some_reference);
    }


    /**
     * Returns the HTML of the memory bar
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The HTML of the parsed template
     * @access protected
     * @static
     */
    protected static function getMemoryBarHtml()
    {
        $memory = array(
            'max-byte'         => (int)ini_get('memory_limit') * 1024 * 1024,
            'max-megabyte'     => (int)ini_get('memory_limit').'M',
            'usage-byte'       => memory_get_peak_usage(false),
            'usage-megabyte'   => round(memory_get_peak_usage() / 1024 / 1024, 0).'M',
            'usage-percentage' => round((memory_get_peak_usage(false) * 100) / (int)ini_get('memory_limit') * 1024 * 1024, 0)
        );

        return self::parseTemplate(self::$_templates['memory-bar'], $memory);
    }

    /**
     * Extracts the name of the class of the current exception out of passed data.
     * If not found it returns the passed default value.
     *
     * @param array  $data    The data to use for check
     * @param string $default The default string to return if no class exists
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The result
     * @access protected
     * @static
     */
    protected static function extractClassFromArray(array $data, $default = '-&nbsp;')
    {
        return (isset($data['class']) && $data['class'] != '') ? $data['class'] : $default;
    }

    /**
     * Converts all data from a passed array of elements to its string representation
     *
     * @param array $data The data to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The converted elements 1:1
     * @access protected
     * @static
     */
    protected static function convertArrayOfPhpTypesToStrings(array $elements)
    {
        foreach ($elements as $element => $value) {
            if (self::isSafe($value)) {
                $elements[$element] = self::realValue($value, gettype($value));
            } else {
                $elements[$element] = '? ('.gettype($value).')';
            }
        }

        return $elements;
    }

    /**
     * Returns the size in ~bytes (not 100% exactly - just estimated).
     *
     * @param mixed $value The input to return size for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The size in bytes
     * @access protected
     * @static
     */
    protected static function getSize($value)
    {
        $type = gettype($value);
        $size = 0;

        if ($type === 'array' || $type === 'object') {
            foreach ($value as $property => $value) {
                $size += self::getSize($property) + self::getSize($value);
            }
        } else {
            $size = mb_strlen($value);
        }

        return $size;
    }

    /**
     * Returns the real value like TRUE in string representation.
     *
     * @param mixed $value The value to convert
     * @param mixed $type  The PHP native type of the value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The size in bytes
     * @access protected
     * @static
     */
    protected static function realValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                $value = ($value == 1) ? 'true' : 'false';
            break;

            case 'array':
            case 'object':
                //$value = var_export($value, true);
                $value = '???';
            break;

            default:
                $value = $value;
            break;
        }

        return $value;
    }

    /**
     * Extracts the signature of a file for the passed function name.
     *
     * @param string $file     The file to parse from
     * @param string $function The name of the function to extract signature for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The parsed signature in an array representation
     * @access protected
     * @static
     */
    protected static function extractSignature($file, $function)
    {
        // @todo: check if file exists before we try to get it contents
        $source    = file_get_contents($file);
        $arguments = array();

        // regular expression to extract details for methods
        $regex = '~
          function                 #function keyword
          \s+                      #any number of whitespaces
          (?P<function_name>.*?)   #function name itself
          \s*                      #optional white spaces
          (?P<parameters>\(.*?\))  #function parameters
          \s*                      #optional white spaces
          (?P<body>\{.*?\})        #body of a function
        ~six';

        if (preg_match_all($regex, $source, $matches)) {
            foreach ($matches['function_name'] as $index => $functionName) {
                if ($function === $functionName) {
                    $arguments = ($matches['parameters'][$index] !== '()') ?
                        explode(',', str_replace('(', '', str_replace(')', '', $matches['parameters'][$index]))) :
                        array();
                }
                break;
            }
        }

        foreach ($arguments as $key => &$argument) {
            $argument = trim($argument);
        }

        return array(
            'function'  => $function,
            'arguments' => $arguments
        );
    }

    /**
     * Extracts details like file, line, class, ... from a passed exception and its trace
     * content. This only works if the trace is filled.
     *
     * @param Exception $exception The exception to extract details from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Array containing the details if successful, otherwise NULL
     * @access protected
     * @static
     */
    protected static function extractDetails(Exception $exception)
    {
        // assumed an empty array as result as default
        $result = array();

        // get trace from exception
        $trace = $exception->getTrace();

        // check if trace if filled correctly
        if ($trace) {
            $first = count($trace)-1;

            $result = array(
                'file'      => (isset($trace[$first]['file']))     ? $trace[$first]['file']     : '&nbsp;',
                'line'      => (isset($trace[$first]['line']))     ? $trace[$first]['line']     : '&nbsp;',
                'class'     => (isset($trace[$first]['class']))    ? $trace[$first]['class']    : '&nbsp;',
                'function'  => (isset($trace[$first]['function'])) ? $trace[$first]['function'] : '&nbsp;',
                'arguments' => (isset($trace[$first]['args']))     ? $trace[$first]['args']     : '&nbsp;',
                'type'      => (isset($trace[$first]['type']))     ? $trace[$first]['type']     : '&nbsp;'
            );
        }

        return $result;
    }

    /**
     * Parses a template and replace variables with passed values (key => value)
     *
     * @param string $template The name of the template to parse
     * @param array  $data     The data to insert (key => value)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The parsed template
     * @access protected
     * @static
     */
    protected static function parseTemplate($template, array $data)
    {
        foreach ($data as $variable => $value) {
            $template = str_replace('{{'.$variable.'}}', $value, $template);
        }

        return $template;
    }
}
