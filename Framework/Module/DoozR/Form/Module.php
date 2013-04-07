<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Module
 *
 * Module.php - Module for generating valid and 100% x-browser compatible
 * HTML-Forms
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
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Module/Singleton/Facade.php';
require_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/Form/Module/Validate.php';

/**
 * DoozR - Form - Module
 *
 * Module.php - Module for generating valid and 100% x-browser compatible
 * HTML-Forms
 *
 * @category   DoozR
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 * @DoozRType  Singleton
 */
final class DoozR_Form_Module extends DoozR_Base_Module_Singleton_Facade
{
    /**
     * The request method (e.g. POST || GET) for faster access
     *
     * @var string
     * @access private
     */
    private $_requestMethod;

    /**
     * The request object (GET || POST) after detecting a submit
     * to prevent duplicate checks for correct source of form values
     * which speeds up the lookup for submitted values
     *
     * @var object
     * @access private
     */
    private $_requestObject;

    /**
     * The form attributes
     *
     * @var array
     * @access private
     */
    private $_attributes = array();

    /**
     * The name of the form
     *
     * @var string
     * @access private
     */
    private $_name;

    /**
     * The valid status form we assume that the status
     * of a form is always valid (TRUE) at the moment
     * of instanciation
     *
     * @var boolean
     * @access private
     */
    //private $_valid = true;
    private $_valid = null;

    /**
     * The state of is_upload if true the encoding
     * is set for file uploads otherwise default
     * encoding is used
     *
     * @var bool
     * @access private
     */
    private $_upload = false;

    /**
     * The max_file_size value for upload-forms
     *
     * @var integer
     * @access private
     */
    private $_maxFileSize = 0;

    /**
     * TRUE if form is validating its items
     * otherwise FALSE
     *
     * @var boolean
     * @access private
     */
    private $_validating = false;

    /**
     * The configuration of the elements of the form
     *
     * @var array
     * @access private
     */
    private $_elements = array();

    /**
     * The current set of field(s)
     *
     * @var array
     * @access private
     */
    private $_currentFieldset;

    /**
     * The fieldset(s) of the form
     *
     * @var unknown_type
     */
    private $_fieldsets;

    /**
     * The count of fieldset(s)
     *
     * @var integer
     * @access private
     */
    private $_fieldsetCount = 0;

    /**
     * The instances of created fields
     *
     * @var array
     * @access private
     */
    private $_fieldInstances;

    /**
     * The count of elements added to form
     *
     * @var integer
     * @access private
     */
    private $_elementCount = 0;

    /**
     * The status of loaded classfiles for types
     *
     * @var array
     * @access private
     */
    private $_loaded = array();

    /**
     * The submittable status of the form
     *
     * @var boolean
     * @access private
     */
    private $_submittable = true;

    /**
     * The status if form was submitted or not
     *
     * @var boolean
     * @access private
     */
    private $_submitted;

    /**
     * NULL by default and filled by calling -> jumped() or isJumped()
     * afterwards TRUE if form is initalized by a jump, otherwise FALSE
     *
     * @var boolean
     * @access private
     */
    private $_jumped;

    /**
     * The form-elements-error if form isn't valid
     * but only form-error like "wrong method used"
     * "invalid token" ...
     *
     * @var array
     * @access private
     */
    private $_error = array();

    /**
     * The form-elements impacts
     *
     * @var array
     * @access private
     */
    private $_impact = array();

    /**
     * The html-code generated for whole form
     *
     * @var string
     * @access private
     */
    private $_html = '';

    /**
     * The localization module for string-translations
     *
     * @var object
     * @access private
     */
    private $_i18n;

    /**
     * The behavior for invalid token submits
     *
     * @var string
     * @access private
     */
    private $_invalidTokenBehavior;

    /**
     * The store to CRUD settings to/from.
     * Currently it's hardcoded the session of PHP,
     * accessed through DoozR_Session_Module
     *
     * @var DoozR_Session_Module
     * @access private
     */
    private $_store;

    /**
     * The configuration of the current form.
     *
     * @var array
     * @access private
     */
    private $_config;

    /**
     * The current step of the form
     * (default is 1)
     *
     * @var integer
     * @access private
     */
    private $_step = self::DEFAULT_STEP;

    /**
     * The current steps of the form including last
     * (default is 1)
     *
     * @var integer
     * @access private
     */
    private $_steps = self::DEFAULT_STEPS;

    /**
     * The prefix for fields, fieldsets ...
     *
     * @var string
     * @access public
     */
    const PREFIX = 'DoozR_Form_Module_';

    /**
     * Token behavior constants
     * DENY = Block access to page (tries to send 404)
     *
     * @var integer
     * @access public
     */
    const TOKEN_BEHAVIOR_DENY = 1;

    /**
     * Token behavior constants
     * IGNORE = No matter if valid or invalid - the token just get ignored
     *
     * @var integer
     * @access public
     */
    const TOKEN_BEHAVIOR_IGNORE = 2;

    /**
     * Token behavior constants
     * DENY = Block access to page (tries to send 404)
     *
     * @var integer
     * @access public
     */
    const TOKEN_BEHAVIOR_INVALIDATE = 3;

    /**
     * The name/identifier of hidden field for submission status
     *
     * @var string
     * @access public
     */
    const SUBMISSION_STATUS_FIELDNAME = 'Submitted';

    /**
     * The maximum upload size for hidden field (MAX_FILE_SIZE) in upload-form
     *
     * @var integer
     * @access public
     */
    const DEFAULT_UPLOAD_MAX_SIZE = 52428800;

    /**
     * The default step if no step is/was set before
     *
     * @var integer
     * @acces public
     */
    const DEFAULT_STEP  = 1;

    /**
     * The default amount of steps till finish is 1 (1/1)
     *
     * @var integer
     * @access public
     */
    const DEFAULT_STEPS = 1;

    /**
     * The default name used at init for default (e.g. for optional I18n namespace)
     *
     * @var string
     * @access public
     */
    const DEFAULT_NAME = 'Form';

    /**
     * The encoding (enctype) for upload-forms -> enctype="multipart/form-data"
     *
     * @var string
     * @access public
     */
    const ENCODING_UPLOAD = 'multipart/form-data';

    /**
     * The encoding (enctype) for default-forms enctype="application/x-www-form-urlencoded"
     *
     * @var string
     * @access public
     */
    const ENCODING_DEFAULT = 'application/x-www-form-urlencoded';

    /**
     * The tabulator replacement
     *
     * @var string
     * @access public
     */
    const TABULATOR = "\t";

    /**
     * The new line replacement
     *
     * @var string
     * @access public
     */
    const NEW_LINE = "\n";


    /**
     * Constructor replacement for modules of DoozR Framework
     *
     * @param string $name The name (identifier) of the form
     * @param object $i18n The I18n module for translations (optional)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access protected
     */
    public function __tearup($name = self::DEFAULT_NAME, DoozR_I18n_Module $i18n = null)
    {
        // get session
        $this->_store = DoozR_Loader_Moduleloader::load('session');

        // store passed arguments
        $this->setName($name);

        // get module for I18n support
        $this->setI18n($i18n);

        // set the max-upload-filesize to value defined in PHP-ini
        $this->_maxFileSize = ini_get('upload_max_filesize');

        // now try to fetch important data about last form-submission if exist
        $this->_lookupRequest();

        // prevent browser and middleware (as long as it recognize the headers)
        // from caching form-content
        $this->_sendHeaders();
    }

    /**
     * This method is intend to search the request and session for submitted data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _lookupRequest()
    {
        // automatic lookup for submitted formdata
    }

    /**
     * Sends no cache headers
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _sendHeaders()
    {
        // mark our patching headers to prevent caching forms by sending x-doozr-form header
        if (sendNoCacheHeaders()) {
            header('x-doozr-form: no-cache');
        }
    }

    /**
     * Validates the store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _validateStore()
    {
        return ($this->_storeRead() !== null);
    }

    /**
     * tries to validate a submitted form
     *
     * This method is intend to validate a submitted form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if submitted form is valid, otherwise FALSE
     * @access public
     */
    public function valid()
    {
        // if _valid is null no detection was run before -> start here
        if ($this->_valid === null && !$this->_validating === true) {

            $this->_validating = true;

            // check FIRST if form was submitted! if not it is always valid!
            if ($this->submitted()) {

                /**
                 * check if store i still valid - session can be timed out ...
                 */
                if (!$this->_validateStore()) {
                    $this->setError('The Form isn\'t valid anymore! Please submit it again ...', 'form');
                    return false;
                }

                /**
                 * 2nd step of all check if correct method was used for submission
                 */
                $method = $this->_storeRead('method');

                if ($method != $this->_getRequestMethod()) {
                    $this->setError('Wrong method used for submitting form! Assumed: "'.$method.'"');
                    return false;
                }

                /**
                 * 3rd step - validate token used for submit
                 */
                if (!$this->_validateToken()) {
                    $this->setError('Invalid token used for submitting form!');
                    return false;
                }

                /**
                 * 4th step - iterate fields and check for individual error(s) if one found
                 * either MISSING, BAD, INCORRECT DATA => FORM INVALID! and error = fields error message
                 */
                $elements = $this->_storeRead('elements');

                // get current element as request-object
                $request = $this->getRequestObject();

                // iterate elements and check impact and valid-status
                foreach ($elements as $name => $config) {
                    $name = str_replace('[]', '', $name);

                    // select the one which currently processed
                    $currentObject = $request->get($name);

                    // check if request-object isset - or if is_null
                    if ($currentObject && $currentObject instanceof DoozR_Request_Value) {
                        //$this->setImpact(123, $name);
                        $this->setImpact($currentObject->getImpact(), $name);
                    }

                    // only validate if validation set
                    if (!empty($config['validation'])) {

                        $value = (isset($request->{$name})) ? $request->{$name} : null;

                        // try to validate the element
                        $result = DoozR_Form_Module_Validate::validate(
                            $value,                                              // the value of submitted element
                            $config['validation'],                               // the array / set of validation(s)
                            $config['type']                                      // the fieldtype
                        );

                        // check if result is TRUE which means field is valid or not
                        // if result is true then the field is valid in all other cases its invalid
                        if ($result !== true) {
                            // store error $result holds the error-array if not true
                            $this->setError($result, $name);
                        }

                        // NOTE: we do not need to set the form status _valid to false cause
                        // this is done by setError()
                    }
                }

                // it's true if reach this point
                $this->_valid = ($this->_valid === null) ? true : $this->_valid;

            } else {
                $this->_valid = true;
            }
        }

        // return current form status
        return $this->_valid;
    }

    /**
     * returns the (sumitted) value of a requested request-variable
     *
     * This method is intend to return the (sumitted) value of a requested request-variable.
     *
     * @param string $variable  The variable-name to get value from
     * @param mixed  $usePrefix True to use the DoozR_Form_Module::PREFIX for $variable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Value of request-variable if set/submitted, otherwise NULL
     * @access public
     */
    public function getSubmittedValue($variable, $usePrefix = false)
    {
        // add DoozR prefix?
        if ($usePrefix) {
            $variable = self::PREFIX.$variable;
        }

        // check if already detected request-method ...
        if (!$this->_requestObject) {
            $this->_detectRequestSource();
        }

        // and return the variables value
        return $this->_requestObject->{$variable};
    }

    /**
     * detects and stores the used request source
     *
     * This method is intend to detect and store the used request source (POST || GET).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _detectRequestSource()
    {
        // get method (post || get)
        $method = $this->_getRequestMethod();

        // get correct source
        switch ($method) {
        case 'post':
            $this->_requestObject = $_POST;
            break;
        case 'get':
        default:
            $this->_requestObject = $_GET;
            break;
        }
    }

    /**
     * returns the request object requested by name
     *
     * This method is intend to return the request object requested by name.
     *
     * @param mixed $name The name of the request or NULL to return the whole request-object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed OBJECT the requested object if exist, otherwise NULL
     * @access public
     */
    public function getRequestObject($name = null)
    {
        // check if already detected request-method ...
        if (!$this->_requestObject) {
            $this->_detectRequestSource();
        }

        // if no name given -> we return the whole request object
        if (!$name) {
            return $this->_requestObject;
        } else {
            // return the "by-name" requested object
            //return ($this->_requestObject->get($name)) ? $this->_requestObject->get($name) : null;

            pred($this->_requestObject);
        }
    }

    /*******************************************************************************************************************
     * // BEGIN - PUBLIC PROPERTY SETTER AND GETTER
     ******************************************************************************************************************/

    /**
     * sets the name of form
     *
     * This method is intend to set the name of form
     *
     * @param string $name The name of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setName($name)
    {
        // store name as class-property
        $this->_name = $name;

        // and as attribute for form-tag (html)
        return $this->setAttribute('name', $name);
    }

    /**
     * sets the name of form
     *
     * This method is intend to set the name of form
     *
     * @param string $name The name of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function name($name)
    {
        return $this->setName($name);
    }

    /**
     * returns the name of form
     *
     * This method is intend to return the name of form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING name of the form if set, otherwise NULL
     * @access public
     */
    public function getName()
    {
        return $this->getAttribute('name');

    }

    /**
     * sets the action (target-script) of form
     *
     * This method is intend to set the action (target-script) of form
     *
     * @param string $action The action to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setAction($action)
    {
        return $this->setAttribute('action', $action);
    }

    /**
     * sets the action (target-script) of form
     *
     * This method is intend to set the action (target-script) of form
     *
     * @param string $action The action to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Module Current running instance
     * @access public
     */
    public function action($action)
    {
        $this->setAction($action);

        // chaining
        return $this;
    }

    /**
     * returns the action (target-script) of form
     *
     * This method is intend to return the action (target-script) of form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING action of the form if set, otherwise NULL
     * @access public
     */
    public function getAction()
    {
        return $this->getAttribute('action');
    }

    /**
     * sets the method (POST | GET | PUT) of form
     *
     * This method is intend to set the method (POST | GET | PUT) of form.
     *
     * @param string $method The method to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setMethod($method)
    {
        $method = strtolower($method);
        return $this->setAttribute('method', $method);
    }

    /**
     * sets the method (POST | GET | PUT) of form
     *
     * This method is intend to set the method (POST | GET | PUT) of form.
     *
     * @param string $method The method to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Module Current running instance
     * @access public
     */
    public function method($method)
    {
        $this->setMethod($method);

        // chaining (less backend view code)
        return $this;
    }

    /**
     * returns the method (POST | GET | PUT) of form
     *
     * This method is intend to return the method (POST | GET | PUT) of form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING method of the form if set, otherwise NULL
     * @access public
     */
    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    /**
     * sets the id of form
     *
     * This method is intend to set the id of form.
     *
     * @param string $id The id of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setId($id)
    {
        return $this->setAttribute('id', $id);
    }

    /**
     * sets the id of form
     *
     * This method is intend to set the id of form.
     *
     * @param string $id The id of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function id($id)
    {
        return $this->setId($id);
    }

    /**
     * returns the the id of form
     *
     * This method is intend to return the id of form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING method of the form if set, otherwise NULL
     * @access public
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * Stores the error of a form-element.
     *
     * @param mixed   $error      The error to set
     * @param string  $element    The name of the element with the error
     * @param boolean $invalidate TRUE to invalidate the whole form, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setError($error, $element = null, $invalidate = true)
    {
        // check if element given
        if ($element) {
            // set error for single element
            $result = ($this->_error[$element] = $error);
        } elseif (is_array($error)) {
            // set error
            $result = ($this->_error = $error);
        } else {
            // default = error for form
            $result = ($this->_error['form'] = $error);
        }

        // invert the invalidate value (true ~ false | false ~ true)
        //$this->_valid = ($this->_valid) && (!$invalidate);
        $status = ($this->_valid) && (!$invalidate);
        $this->_setValid($status);

        // return the result
        return $result;
    }

    /**
     * Stores the impact of a form-element.
     *
     * @param mixed   $impact     The impact to set
     * @param mixed   $element    The identifier invalid element
     * @param boolean $invalidate TRUE to invalidate the whole form, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setImpact($impact, $element = null, $invalidate = true)
    {
        // check if element given
        if ($element) {
            // set error for single element
            $result = ($this->_impact[$element] = $impact);
        } elseif (is_array($impact)) {
            // set error
            $result = ($this->_impact = $impact);
        } else {
            // default = error for form
            $result = ($this->_impact['form'] = $impact);
        }

        // invert the invalidate value (true ~ false | false ~ true) - but note:
        // only if impact values greater 0 influent the validity of the form !!!
        if ($impact > 0) {
            //$this->_valid = ($this->_valid) && (!$invalidate);
            $this->_setValid(($this->_valid) && (!$invalidate));
        }

        // return the result
        return $result;
    }

    /**
     * Returns the error of form.
     *
     * @param string $element The element for which the error should be returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING error if set, otherwise NULL
     * @access public
     */
    public function getError($element = null)
    {
        // is a special element requested?
        if ($element) {
            return (isset($this->_error[$element])) ? $this->_error[$element] : null;
        } else {
            // return all existing error // maybe none
            return $this->_error;
        }
    }

    /**
     * Returns the impacts of form-elements.
     *
     * @param string $element The element for which the impact should be returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Integer impact if set, otherwise NULL
     * @access public
     */
    public function getImpact($element = null)
    {
        // is a special element requested?
        if ($element) {
            return (isset($this->_impact[$element])) ? $this->_impact[$element] : null;
        } else {
            // return all existing error // maybe none
            return $this->_impact;
        }
    }

    /**
     * Returns the last occured error of current form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING error if $plain is set to TRUE, otherwise array holding information or NULL if no error
     * @access public
     */
    public function getLastError()
    {
        // process only if error(s) exists
        if (!empty($this->_error)) {
            // just the error requested? ...
            /*
            if ($plainError) {
                return end($this->_error);
            } else {
                */
                // ... otherwise return keyed array with element AND error
                return array(
                    'element' => @end(array_keys($this->_error)),
                    'error'   => end($this->_error)
                );
            //}
        } else {
            // no error => no result ~ null
            return null;
        }
    }

    /**
     * Alias for setI18n()
     *
     * @param DoozR_I18n_Module $i18n The i18n instance of the DoozR_I18n_Module or compatible interface
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING error if $plain is set to TRUE, otherwise array holding information or NULL if no error
     * @access public
     */
    public function i18n($i18n)
    {
        $this->setI18n($i18n);

        // chaining
        return $this;
    }

    /**
     * Sets the i18n instance for translations.
     *
     * @param DoozR_I18n_Module $i18n The i18n instance of the DoozR_I18n_Module or compatible interface
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setI18n($i18n)
    {
        if ($i18n) {
            $this->_i18n = $i18n->getTranslator();
            $this->_i18n->setNamespace(self::PREFIX.$this->getName());
            return true;
        }

        return false;
    }

    /**
     * makes the form an upload-form
     *
     * This method is intend to make the form an upload-form.
     *
     * @param boolean $upload      TRUE to make form an upload-form, otherwise FALSE
     * @param integer $maxFileSize The maximum filesize for file-upload
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setUpload($upload = true, $maxFileSize = null)
    {
        $this->_upload = $upload;
        $this->_maxFileSize = ($maxFileSize) ? $maxFileSize : $this->_maxFileSize;

        // return success
        return true;
    }

    /**
     * makes the form an upload-form
     *
     * This method is intend to make the form an upload-form.
     *
     * @param boolean $upload      TRUE to make form an upload-form, otherwise FALSE
     * @param integer $maxFileSize The maximum filesize for file-upload
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function upload($upload = true, $maxFileSize = self::DEFAULT_UPLOAD_MAX_SIZE)
    {
        return $this->setUpload($upload, $maxFileSize);
    }

    /**
     * returns true if form is an file-upload form, false if not
     *
     * This method is intend to return true if form is an file-upload form, false if not.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if is upload, otherwise FALSE
     * @access public
     */
    public function getUpload()
    {
        return $this->_upload;
    }

    /**
     * returns true if form is an file-upload form, false if not
     *
     * This method is intend to return true if form is an file-upload form, false if not.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if form is upload form, otherwise FALSE
     * @access public
     */
    public function isUpload()
    {
        return $this->_upload;
    }

    /**
     * sets the maximum filesize for file-uploads
     *
     * This method is intend to sets the maximum filesize for file-uploads.
     *
     * @param integer $maxFileSize The maximum filesize for file-upload
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successfully set, otherwise FALSE
     * @access public
     */
    public function setUploadMaxFilesize($maxFileSize = self::DEFAULT_UPLOAD_MAX_SIZE)
    {
        return ($this->_maxFileSize = $maxFileSize);
    }

    /**
     * Sets the current step of the form and passing step is optional.
     * If no step passed the form tries to detect its current step by
     * simple logic.
     *
     * @param integer $step The (optional) step to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Module Current active instance for chaining
     * @access public
     */
    public function step($step = null)
    {
        $this->setStep($step);
        return $this;
    }

    /**
     * Sets the current step of the form and passing step is optional.
     * If no step passed the form tries to detect its current step by
     * simple logic.
     *
     * @param integer $step The (optional) step to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setStep($step = null)
    {
        if ($step === null) {
            if ($this->submitted()) {
                // get current request
                $request = $this->getRequestObject();

                // try to get from last submit -> fallback to default of module
                $step = (isset($request->{self::PREFIX.'Step'})) ? $request->{self::PREFIX.'Step'} : self::DEFAULT_STEP;

                // increment by 1 if form was submitted! valid! and not finished yet!
                $step += ($this->valid()) ? 1 : 0;

            } else {
                // if form wasn't submitted > its default state is step 1
                $step = self::DEFAULT_STEP;
            }
        }

        return ($this->_step = $step);
    }

    /**
     * Returns the current step of the form.
     *
     * @param string $identifier The identifier/name of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function getStep($identifier = null)
    {
        // check if form was submitted before, is valid and not finished
        if ($this->submitted($identifier)) {
            $step = $this->getRequestObject()[self::PREFIX.'Step'];

            // increment by 1 if form was submitted! valid! and not finished yet!
            $step += ($this->valid()) ? 1 : 0;

        } else {
            $step = $this->_step;
        }

        return (int)$step;
    }

    /**
     * Sets the number of steps the current has (for automatic finish
     * detection e.g. pagination ...).
     *
     * @param mixed $steps The steps to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Module Current active instance for chaining
     * @access public
     */
    public function steps($steps = null)
    {
        $this->setSteps($steps);
        return $this;
    }

    /**
     * Sets the number of steps the current has (for automatic finish
     * detection e.g. pagination ...).
     *
     * @param mixed $steps The steps to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setSteps($steps = self::DEFAULT_STEPS)
    {
        return ($this->_steps = $steps);
    }

    /**
     * Returns the max steps of the form.
     *
     * @param string $identifier The identifier/name of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function getSteps($identifier = null)
    {
        // check if form was submitted before, is valid and not finished
        if ($this->submitted($identifier)) {
            $steps = $this->getRequestObject()[self::PREFIX.'Steps'] + 0;
        } else {
            $steps = $this->_steps;
        }

        return (int)$steps;
    }

    /**
     * Returns true if form is finished (reached page x from y)
     *
     * @param integer $steps The number of steps of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if form is finished, otherwise FALSE
     * @access public
     */
    public function finished($steps = 1)
    {
        if ($this->submitted()) {
            if ($this->valid()) {
                $submittedData = $this->getRequestObject();
                $step          = ($submittedData[self::PREFIX.'Step']) ?
                                 (int)$submittedData[self::PREFIX.'Step'] :
                                 (int)$this->_step;
                $steps         = ($submittedData[self::PREFIX.'Steps']) ?
                                 (int)$submittedData[self::PREFIX.'Steps'] :
                                 (int)$this->_steps;

                if ($step >=  $steps) {
                    return true;
                }
            }
        }

        // not yet finished
        return false;
    }

    /**
     * sets the maximum filesize for file-uploads
     *
     * This method is intend to sets the maximum filesize for file-uploads.
     *
     * @param integer $maxFileSize The maximum filesize for file-upload
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successfully set, otherwise FALSE
     * @access public
     */
    public function uploadMaxFilesize($maxFileSize = self::DEFAULT_UPLOAD_MAX_SIZE)
    {
        return $this->setUploadMaxFilesize($maxFileSize);
    }

    /**
     * returns the maximum filesize for file-uploads
     *
     * This method is intend to return the maximum filesize for file-uploads.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The maximum size in bytes for file-uploads
     * @access public
     */
    public function getUploadMaxFilesize()
    {
        return $this->_maxFileSize;
    }

    /**
     * sets the attribute with given value
     *
     * This method is intend to set the attribute with given value.
     *
     * @param string $attribute The attribute to set
     * @param mixed  $value     The value of the attribute
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed MIXED value if set, otherwise NULL
     * @access public
     */
    public function setAttribute($attribute, $value = null)
    {
        $this->_attributes[$attribute] = $value;
        return $this;
    }

    /**
     * sets the attribute with given value
     *
     * This method is intend to set the attribute with given value.
     *
     * @param string $attribute The attribute to set
     * @param mixed  $value     The value of the attribute
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed MIXED value if set, otherwise NULL
     * @access public
     */
    public function attribute($attribute, $value = null)
    {
        return $this->setAttribute($attribute, $value);
    }

    /**
     * returns the value of a requested attribute
     *
     * This method is intend to return the value of a requested attribute if set.
     *
     * @param string $attribute The attribute to return value from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed MIXED value if set, otherwise NULL
     * @access public
     */
    public function getAttribute($attribute)
    {
        return (isset($this->_attributes[$attribute])) ? $this->_attributes[$attribute] : null;
    }

    /**
     * sets submittable status of form
     *
     * This method is intend to set the submittable status the form.
     *
     * @param boolean $submittable TRUE if form should be submittable, otherwise FALSE to prevent submit
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setSubmittable($submittable = false)
    {
        $this->_submittable = $submittable;

        if (!$submittable) {
            return $this->setAttribute('onsubmit', 'javascript:return false;');
        } else {
            return $this->removeAttribute('onsubmit');
        }
    }

    /**
     * sets submittable status of form
     *
     * This method is intend to set the submittable status the form.
     *
     * @param boolean $submittable TRUE if form should be submittable, otherwise FALSE to prevent submit
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function submittable($submittable = false)
    {
        return $this->setSubmittable($submittable);
    }

    /**
     * returns the submittable status of the form
     *
     * This method is intend to return the submittable status the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed boolean TRUE if submittable, otherwise FALSE
     * @access public
     */
    public function getSubmittable()
    {
        return $this->_submittable;
    }

    /**
     * sets the accept MIME-type of the form
     *
     * This method is intend to set the accept mime-type of the form.
     *
     * @param string $accept The accepted MIME-type for fileuploads with this form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setAccept($accept)
    {
        return $this->setAttribute('accept', $accept);
    }

    /**
     * sets the accept MIME-type of the form
     *
     * This method is intend to set the accept mime-type of the form.
     *
     * @param string $accept The accepted MIME-type for fileuploads with this form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function accept($accept)
    {
        return $this->setAccept($accept);
    }

    /**
     * returns the accept MIME-type of the form
     *
     * This method is intend to return the accept mime-type of the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING accept MIME-type if set, otherwise NULL
     * @access public
     */
    public function getAccept()
    {
        return $this->getAttribute('accept');
    }

    /**
     * sets the accept-charset of the form
     *
     * This method is intend to set the accept-charset of the form.
     *
     * @param string $acceptCharset The accepted charset of this form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setAcceptCharset($acceptCharset)
    {
        return $this->setAttribute('accept-charset', $acceptCharset);
    }

    /**
     * sets the accept-charset of the form
     *
     * This method is intend to set the accept-charset of the form.
     *
     * @param string $acceptCharset The accepted charset of this form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function acceptCharset($acceptCharset)
    {
        return $this->setAcceptCharset($acceptCharset);
    }

    /**
     * returns the accept charset of the form
     *
     * This method is intend to return the accept charset of the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING the accept charset if set, otherwise NULL
     * @access public
     */
    public function getAcceptCharset()
    {
        return $this->getAttribute('accept-charset');
    }

    /**
     * sets the encoding-type of the form
     *
     * This method is intend to set the encoding-type of the form.
     *
     * @param string $enctype The encoding-type of this form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setEnctype($enctype)
    {
        return $this->setAttribute('enctype', $enctype);
    }

    /**
     * sets the encoding-type of the form
     *
     * This method is intend to set the encoding-type of the form.
     *
     * @param string $enctype The encoding-type of this form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function enctype($enctype)
    {
        return $this->setEnctype($enctype);
    }

    /**
     * returns the encoding-type of the form
     *
     * This method is intend to return the encoding-type of the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING the encoding-type if set, otherwise NULL
     * @access public
     */
    public function getEnctype()
    {
        return $this->getAttribute('enctype');
    }

    /**
     * returns the count of currently added elements
     *
     * This method is intend to return the count of currently added elements.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Count of currently added elements
     * @access public
     */
    public function getElementCount()
    {
        return $this->_elementCount;
    }

    /**
     * returns the count of currently added fieldsets
     *
     * This method is intend to return the count of currently added fieldsets.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Count of currently added fieldsets
     * @access public
     */
    public function getFieldsetCount()
    {
        return $this->_fieldsetCount;
    }

    /*******************************************************************************************************************
     * \\ END - PUBLIC PROPERTY SETTER AND GETTER
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN - HELPER METHODS
     ******************************************************************************************************************/

    /**
     * Removes an attribute from input-field.
     *
     * @param string $attribute The attributes name to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute was successful removed, otherwise FALSE
     * @access public
     */
    public function removeAttribute($attribute)
    {
        if (isset($this->_attributes[$attribute])) {
            unset($this->_attributes[$attribute]);
        }

        // return success
        return true;
    }

    /**
     * Returns TRUE if the form is jumped to a specific step,
     * otherwise FALSE
     *
     * @param string $identifier The (optional) identifier
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if the form is jumped to a specific step, otherwise FALSE
     * @access public
     */
    public function jumped($identifier = null)
    {
        // use intenal identifier if set
        (!$identifier) ? $identifier = $this->_name : $this->_name = $identifier;

        // check already done?
        if ($this->_jumped !== null) {
            return $this->_jumped;

        } else {
            // get method used for submit
            $request = $this->registry->front->getRequest();
            $request->GET();
            $source = $_GET;

            // and now check if submission identifier exists in current request
            if (isset($source->{self::PREFIX.'Step'})) {
                $this->_jumped = true;
            } else {
                $this->_jumped = false;
            }
        }

        // submission status
        return $this->_jumped;
    }

    /**
     * returns true if a form was submitted and the request contains its data and otherwise false
     *
     * This method is intend to return the submission-status of the current request.
     *
     * @param string $identifier The name of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if a form was submitted, otherwise FALSE
     * @access private
     */
    public function submitted($identifier = null)
    {
        // use intenal identifier if set
        (!$identifier) ? $identifier = $this->_name : $this->_name = $identifier;

        // check already done?
        if ($this->_submitted !== null) {
            return $this->_submitted;

        } else {
            // get method used for submit
            $method = $this->_getRequestMethod();
            $request = $this->registry->front->getRequest();

            switch ($method) {
            case 'post':
                $request->POST();
                $source = $_POST;
                break;
            case 'get':
                $request->GET();
                $source = $_GET;
                break;
            }

            // and now check if submission identifier exists in current request
            if ($source->{self::PREFIX.self::SUBMISSION_STATUS_FIELDNAME}() === $identifier) {
                $this->_submitted = true;
            } else {
                $this->_submitted = false;
            }
        }

        // submission status
        return $this->_submitted;
    }

    /**
     * adds an button to form
     *
     * This method is intend to add a button to form.
     *
     * @param string  $type            The type of element to add
     * @param boolean $setDivContainer Defines if a div-container should surround the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The instance of the element's class
     * @access public
     */
    public function addButton($type = 'submit', $setDivContainer = false)
    {
        return $this->addElement($type, $setDivContainer);
    }

    /**
     * adds an element (input-field, button, img, ...) to form
     *
     * This method is intend to add an element (input-field, button, img, ...) to form.
     *
     * @param string  $type              The type of element to add
     * @param boolean $setDivContainer   Defines if a div-container should surround the input-field
     * @param mixed   $divContainerClass The class to set for surrounding container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The instance of the element's class
     * @access public
     */
    public function add($type = 'text', $setDivContainer = false, $divContainerClass = null)
    {
        return $this->addElement($type, $setDivContainer, $divContainerClass);
    }

    /**
     * returns a collection of form-elements (instances of form-elements)
     *
     * This method is intend to return a collection of form-elements.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array A collection of form-element instances added previously
     * @access public
     */
    public function getFields()
    {
        return $this->_fieldInstances;
    }

    /**
     * adds an element (input-field, button, img, ...) to form
     *
     * This method is intend to add an element (input-field, button, img, ...) to form.
     *
     * @param string  $type              The type of element to add
     * @param boolean $setDivContainer   Defines if a div-container should surround the input-field
     * @param mixed   $divContainerClass The class to set for surrounding container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The instance of the element's class
     * @access public
     */
    public function addElement($type = 'text', $setDivContainer = false, $divContainerClass = null)
    {
        // need lowercase for further processing
        $type = strtolower($type);

        // get correct type
        $className = $this->_getClassification($type);

        // load class
        $this->_loadClassfile($className);

        // if input is of type file set form-type to upload
        if ($type == 'file') {
            $this->setUpload();
        }

        // create array of parameter
        $config = array(
            'classname'      => $className,
            'type'           => $type,
            'parent'         => $this->getName(),
            'error'          => $this->getError(),
            'impact'         => $this->getImpact(),
            'submitted'      => ($this->submitted() && $this->_validateStore()),
            'jumped'         => ($this->jumped() && $this->_validateStore()),
            'requestMethod'  => $this->_getRequestMethod(),
            'container'      => $setDivContainer,
            'containerclass' => $divContainerClass,
            'i18n'           => $this->_i18n,
            'form'           => $this
        );

        // check if a fieldset exists - if not -> create
        if (!$this->_currentFieldset) {
            // begin a new/blank fieldset
            $this->setFieldsetBegin(
                self::PREFIX.'Fieldset_'.$this->_fieldsetCount,
                null,
                self::PREFIX.'Fieldset',
                $type
            );

            // create instance of field and store fieldinstance in list of fields for current fieldset
            $this->_createFormElement($config);
            $fieldInstance = $this->_getLastFormElementReference();

            $this->setFieldsetEnd();

            // increase count of used fieldsets (automatic generated fieldsets)
            $this->_fieldsetCount++;
        } else {
            // if fieldset exists -> append
            $this->_createFormElement($config);
            $fieldInstance = $this->_getLastFormElementReference();
        }

        // increase count of elements
        ++$this->_elementCount;

        // store fields for name checks ...
        $this->_fieldInstances[] = $fieldInstance;

        // return fresh created instance of the form field
        return $fieldInstance;
    }

    /**
     * smart hack => just call 'echo $form' instead of $form->render(...);
     *
     * This method is a smart hack like described above
     *
     * @return string The rendered HTML-Code of the form
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __toString()
    {
        return $this->render($this->_name);
    }

    /**
     * creates the form (main)
     *
     * This method is intend to create the first important elements of the form. This is the main-method for
     * form-creation.
     *
     * @param string $name      The name of the form
     * @param string $formModel The form-model to use for this form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Module Current running instance for chaining
     * @access public
     */
    public function create($name, $formModel = null)
    {
        // store the name
        $this->setName($name);

        // check if form-model is given
        if ($formModel) {
            // try to parse
            $this->_parseModel($formModel);
        }

        // chaining
        return $this;
    }

    /**
     * returns the valid-status of the form
     *
     * This method is intend to return the valid-status of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if form is valid, otherwise FALSE
     * @access public
     */
    public function isValid()
    {
        return $this->_valid;
    }

    /**
     * sets the behavior for case of invalid token submitted
     *
     * This method is intend to set the behavior for case of invalid token submitted
     *
     * @param string $behavior The behavior to set (can be either IGNORE, DENY, INVALIDATE)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function onInvalidToken($behavior)
    {
        // store the behavior and return result
        $this->_invalidTokenBehavior = $behavior;
        return $this;
    }

    /**
     * validates the submitted token of the form
     *
     * This method is intend to validate the submitted token of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function validateToken()
    {
        $this->_validateToken();
    }

    /**
     * returns the current configured behavior for invalid tokens
     *
     * This method is intend to return the current configured behavior for invalid tokens.
     *
     * @param integer $override The value to override the stored token-behavior with
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The current configured behavior
     * @access public
     */
    public function getInvalidTokenBehavior($override = null)
    {
        // if not already configured check if behavior was stored in a previous request
        if (!$this->_invalidTokenBehavior) {
            $this->_invalidTokenBehavior = $this->_storeRead('tokenbehavior');

            if (!$this->_invalidTokenBehavior && $override) {
                $this->_invalidTokenBehavior = $override;
            }
        }

        // return the behavior
        return $this->_invalidTokenBehavior;
    }

    /**
     * opens/begins a fieldset (group of form-fields)
     *
     * This method is intend to open/begin a fieldset (group of form-fields).
     *
     * @param string $id     The id of the fieldset
     * @param string $legend The text for the fieldsets legend
     * @param string $class  The class to set to fieldset (e.g. <fieldset class="ABC"...)
     * @param string $type   The input-field (element) type
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFieldsetBegin($id = null, $legend = null, $class = '', $type = null)
    {
        // check for id
        if (!$id) {
            $id = md5(time());
        }

        // set current fieldset's id
        $this->_currentFieldset = $id;

        // create framework for current fieldset
        $this->_fieldsets[$id] = array(
            'id'      => $id,
            'class'   => $class,
            'legend'  => $legend,
            'hidden'  => ($type == 'hidden') ? true : false,
            'objects' => array()
        );

        // chaining
        return $this;
    }

    /**
     * closes/ends a fieldset (group of form-fields)
     *
     * This method is intend to close/end the last opened fieldset (group of form-fields).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFieldsetEnd()
    {
        $this->_currentFieldset = null;
    }

    /**
     * closes/ends a fieldset (group of form-fields)
     *
     * This method is intend to close/end the last opened fieldset (group of form-fields).
     *
     * @param string  $identifier The identifier of the form to render
     * @param boolean $output     TRUE to echo the HTML-code, FALSE to retrieve it as return value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function render($identifier, $output = false)
    {
        // prefix
        $pre = self::PREFIX;

        // check for form encoding for upload and mx-filesize
        $this->_configureUpload();

        // inject hidden-field TOKEN to prevent form abuse + step + steps
        $this->_addTokenField();
        $this->_addStepField();
        $this->_addStepsField();

        // begin building Form-HTML by adding the form tag
        $this->_html = $this->t().'<form class="'.self::PREFIX.'Form"';

        //
        $this->attribute('generated', 'true');

        // add basic form attributes
        foreach ($this->_attributes as $attribute => $value) {
            $this->_html .= ' '.$attribute.'="'.$value.'"';
        }

        // close form tag and add a new-line
        $this->_html .= '>'.$this->nl();

        // add the field used to detect submitted DoozR_Form_Module(s)
        $this->_html .= $this->t(2).
        '<input type="hidden" name="'.$pre.self::SUBMISSION_STATUS_FIELDNAME.
        '" id="'.$pre.self::SUBMISSION_STATUS_FIELDNAME.'" value="'.$this->_name.
        '" />'.$this->nl();

        // iterate over fieldsets and the contained elements
        foreach ($this->_fieldsets as $fieldset) {

            // if fieldset is visible add it
            if (!$fieldset['hidden']) {
                $this->_html .= $this->t(2).'<fieldset id="'.$fieldset['id'].'" class="'.$fieldset['class'].'">'.
                                $this->nl();

                // check for legend for fieldset
                if ($fieldset['legend']) {
                    $this->_html .= $this->t(2).'<legend>'.$fieldset['legend'].'</legend>'.$this->nl();
                }
            }

            // iterate over fieldset's form-elements
            foreach ($fieldset['objects'] as $element) {
                // process the element
                $element = $this->_processFormElement($element);

                // store the validation config
                $this->_elements[$element['name']] = array(
                                'type'       => $element['type'],
                                'validation' => $element['validation']
                );

                // concat elements html-code
                $this->_html .= $element['html'].$this->nl();
            }

            if (!$fieldset['hidden']) {
                $this->_html .= $this->t(2).'</fieldset>'.$this->nl();
            }
        }

        // all fields added close form tag and add a new-line
        $this->_html .= $this->t().'</form>'.$this->nl();

        // store backup data from step before?
        $this->_manageDataTransfer();

        // store fieldset-configuration
        $this->_storeWrite('elements', $this->_elements);
        $this->_storeWrite('method', $this->getMethod());
        $this->_storeWrite('tokenbehavior', $this->getInvalidTokenBehavior(self::TOKEN_BEHAVIOR_IGNORE));

        // developer want's`?
        if ($output) {
            // output
            echo $this->_html;
        } else {
            // return
            return $this->_html;
        }
    }

    /**
     * Returns all collected data from user input frm step 1 till now (step x)
     *
     * @param boolean $mergeSteps TRUE to merge a single steps to one resulting array, FALSE to do not (default)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The collected data
     * @access public
     */
    public function getData($mergeSteps = false)
    {
        // trigger data transfer before each data request to include user input
        // from last request
        $this->_manageDataTransfer();

        // get backpack value from store
        $backpack = ($this->_storeRead('backpack') !== null) ? $this->_storeRead('backpack') : array();

        // if merge is requested -> we merge all data into one huge
        // array -> values from larger steps win
        if ($mergeSteps === true) {
            $merged = array();
            foreach ($backpack as $step => $elements) {
                foreach ($elements as $element => $value) {
                    $merged[$element] = $value;
                }
            }
            $backpack = $merged;
        }

        return $backpack;
    }

    /**
     * returns tabulator control-char
     *
     * This method is intend to return tabulator control-char (for formatting html ouput).
     *
     * @param integer $count Defines how many tabulator control-chars should be returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested amount of tabulator control-chars
     * @access protected
     */
    protected function t($count = 1)
    {
        return str_repeat(self::TABULATOR, $count);
    }

    /**
     * returns new-line control-char
     *
     * This method is intend to return new-line control-char (for formatting html ouput).
     *
     * @param integer $count Defines how many new-line control-chars should be returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested amount of new-line control-chars
     * @access protected
     */
    protected function nl($count = 1)
    {
        return str_repeat(self::NEW_LINE, $count);
    }

    /**
     * parse a form-model from given name
     *
     * This method is intend to parse a form-model from a given name
     *
     * @param string $name The name of the form-model
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _parseModel($name)
    {
        // holds the parts
        $parts = explode('_', $name);

        // holds the path
        $path = '';

        // iterate over parts and construct path
        foreach ($parts as $key => $part) {
            $path .= $part.DIRECTORY_SEPARATOR;
        }

        // build full qualified path to model of form
        $file = $path.str_replace(DIRECTORY_SEPARATOR, '', $path).'.php';
        $file = $this->registry->path->get('app').'Model'.DIRECTORY_SEPARATOR.$file;

        // include the file
        include_once $file;
    }

    /**
     * Manages the data transfer between steps no matter if forward
     * or backward. This method ensures that the user input is transfered
     * between steps.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _manageDataTransfer()
    {
        $backpack = ($this->_storeRead('backpack') !== null) ? $this->_storeRead('backpack') : array();

        if ($this->submitted() && $this->valid()) {
            // get items identifier to extract and backup them in store
            $setup    = $this->_storeRead();
            $request  = $this->getRequestObject();
            $step     = ($this->getStep() > 1) ? $this->getStep()-1 : $this->getStep();
            $keyValue = array();

            foreach ($setup['elements'] as $id => $setup) {
                $keyValue[$id] = (isset($request->{$id})) ? $request->{$id} : null;
            }

            if (isset($backpack[$step])) {
                $keyValue = array_merge($backpack[$step], $keyValue);
            }

            $backpack[$step] = $keyValue;
        }

        $this->_storeWrite('backpack', $backpack);
    }

    /**
     * set the valid-status of the form
     *
     * This method is intend to set the valid-status of the form
     *
     * @param boolean $status The status of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _setValid($status = true)
    {
        $this->_valid = $status;
    }

    /**
     * manages the token-logic
     *
     * This method is intend to manage the token-logic. It checks if token was given as assumed and if it's valid.
     * It also removes used tokens from list of valid tokens and cancel requests without valid tokens.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if token could be validated (valid), otherwise FALSE
     * @access private
     */
    private function _validateToken()
    {
        // 1st get valid token from store
        $validToken = $this->_storeRead('token');

        // 2nd get submitted token from request
        $submittedToken = $this->getSubmittedValue('Token', true);

        // check if submitted token is exactly the same as previously stored
        $tokenValid = ($submittedToken === $validToken) ? true : false;

        // assume that token is valid - all other possible ...
        $status = true;

        // get behavior configured in previous request
        $invalidTokenBehavior = $this->getInvalidTokenBehavior();

        // remove token
        $this->_storeDelete('token');

        // check for configured behavior
        switch ($invalidTokenBehavior) {
        case self::TOKEN_BEHAVIOR_IGNORE:
            break;

        case self::TOKEN_BEHAVIOR_INVALIDATE:
            if (!$tokenValid) {
                $status = false;
            }
            break;

        default:
        case self::TOKEN_BEHAVIOR_DENY:
            if (!$tokenValid) {
                $status = false;

                // try to send correct 404 status ...
                try {
                    $this->registry->front->getResponse()->sendHTTPStatus(400);
                } catch (Exception $e) {
                    // ... if this fails (header already sent) break execution - hard
                    exit;
                }
            }
            break;
        }

        // and return the status
        return $status;
    }

    /**
     * generates a token for the current form and store it in session
     *
     * This method is intend to generate and store a token for the current form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if token was successfully generated and stored, otherwise FALSE
     * @access private
     */
    private function _generateToken()
    {
        // get unique input
        $elementCount = $this->getElementCount();
        $ip           = $_SERVER['REMOTE_ADDR'];
        $userAgent    = $_SERVER['HTTP_USER_AGENT'];
        $salt         = $this->_salt();
        $name         = $this->getName();

        // generate token from unqiue input
        $token = md5($elementCount.$ip.$userAgent.$name.$salt);

        // store generated token of this form
        $this->_storeWrite('token', $token);

        // and return it (for HTML generating)
        return $token;
    }

    /**
     * returns a random seed-value
     *
     * This method is intend to return a random seed-value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Random seed value
     * @access private
     */
    private function _salt()
    {
        srand(time());
        return md5(rand(0, 9999));
    }

    /**
     * returns the classification of the requested input-field
     *
     * This method is intend to return the classification of the requested input-field.
     *
     * @param string $type The form element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The type (class) of the form-element
     * @access private
     */
    private function _getClassification($type)
    {
        // check which type given ...
        // this can be text, submit, checkbox, radio, file, image, password, button, reset OR select, textarea
        switch ($type) {
        // special input
        case 'label':
        case 'textarea':
        case 'select':
        case 'swfupload':
        case 'radio':
        case 'checkbox':
        case 'html':
        case 'file':
        case 'image':
            $value = ucfirst($type);
            break;

        // generic input
        case 'submit':
        case 'reset':
        case 'button':
        case 'text':
        case 'password':
        default:
            $value = 'Input';
            break;
        }

        return $value;
    }

    /**
     * loads and cache loading status of class-files
     *
     * This method is intend to load and cache loading status of class-files.
     *
     * @param string $type The input-field type
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access private
     */
    private function _loadClassfile($type)
    {
        if (!isset($this->_loaded[$type])) {
            // include the needed class file
            include_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/Form/Module/Element/'.$type.'.php';

            // set loaded true
            $this->_loaded[$type] = true;
        }

        // success
        return true;
    }

    /**
     * returns the request-method of current request (GET | POST | PUT)
     *
     * This method is intend to return the request-method of current request (GET | POST | PUT).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The request-method of current request
     * @access public
     */
    private function _getRequestMethod()
    {
        // retrieve if not already done ...
        if (!$this->_requestMethod) {
            //$this->_requestMethod = 'get';
            $this->_requestMethod = strtolower($this->registry->front->getRequest()->getRequestMethod());
        }

        // return stored request-method (e.g. POST || GET)
        return $this->_requestMethod;
    }

    /**
     * creates an instance of form-element requested by config
     *
     * This method is intend to create an instance of form-element requested by config.
     *
     * @param array $config The config for creating the element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _createFormElement(array $config)
    {
        $this->_fieldsets[$this->_currentFieldset]['objects'][] = new $config['classname']($config);
    }

    /**
     * returns the reference to the last created instance of a form-element
     *
     * This method is intend to return the reference to the last created instance of a form-element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The reference to the last created instance if a form element
     * @access private
     */
    private function _getLastFormElementReference()
    {
        $index = count($this->_fieldsets[$this->_currentFieldset]['objects'])-1;
        $reference = &$this->_fieldsets[$this->_currentFieldset]['objects'][$index];
        return $reference;
    }

    /**
     * configures the form for upload if form contains a file-upload-field
     *
     * This method is intend configure the form for upload if form contains a file-upload-field.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _configureUpload()
    {
        // is form of type upload (at min. one file upload field)
        if ($this->_upload) {
            // we must set the encoding to "" for file-upload forms
            $this->_attributes['enctype'] = self::ENCODING_UPLOAD;

            // and now add the hidden-field "MAX_FILE_SIZE" (magic form field)
            $elementMaxSize = $this->addElement('hidden');
            $elementMaxSize->setName('MAX_FILE_SIZE');
            $elementMaxSize->setValue($this->_maxFileSize);
        } else {
            // not an upload form
            $this->_attributes['enctype'] = self::ENCODING_DEFAULT;
        }
    }

    /**
     * adds token-element to the form for upload if form contains a file-upload-field
     *
     * This method is intend configure the form for upload if form contains a file-upload-field.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _addTokenField()
    {
        // add a hidden form-element
        $elementToken = $this->addElement('hidden');

        // set name of hidden field
        $elementToken->setName(self::PREFIX.'Token');

        // set the elements value to a new generated token
        $elementToken->setValue($this->_generateToken(), true);
    }

    /**
     * Adds a hidden field to the form to mark the
     * current step of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The result of the processing
     * @access private
     */
    private function _addStepField()
    {
        //$step = null
        //($step === null) ? $step = $this->_step : $this->_step = $step;

        // add a hidden form-element
        $elementStep = $this->addElement('hidden');

        // set name of hidden field
        $elementStep->setName(self::PREFIX.'Step');

        // set the elements value to the passes step value
        $elementStep->setValue($this->_step, true);
    }

    /**
     * Adds a hidden field to the form to mark the
     * last step of the form
     *
     * @param mixed $steps The last step
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The result of the processing
     * @access private
     */
    private function _addStepsField($steps = null)
    {
        ($steps === null) ? $steps = $this->_steps : $this->_steps = $steps;

        // add a hidden form-element
        $elementSteps = $this->addElement('hidden');

        // set name of hidden field
        $elementSteps->setName(self::PREFIX.'Steps');

        // set the elements value to the passes step value
        $elementSteps->setValue($steps, true);
    }

    /**
     * processes a form element and creates it validation, html ...
     *
     * This method is intend to process a form element and creates it validation, html ...
     *
     * @param object $element The element to process
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The result of the processing
     * @access private
     */
    private function _processFormElement($element)
    {
        // the elements name
        $name = $element->getName();

        // the elements type
        $type = $element->getType();

        // add automatic validation(s)
        if ($type == 'checkbox') {
            // can have exactly one correct value
            $element->addValidate('value', $element->getValue());

            // get validation of element
            $validation = $element->getValidate();

        } elseif ($type == 'radio') {
            // this both fieldtypes can have a multiple of correct values
            $element->addValidate('value', $element->getValue());

            // now check if the fields validation was already stored
            if ($this->_fieldValidationExist($name)) {
                // 1st get existing validation from field
                $validation = $element->getValidate();

                // merge the validation already stored previously and the validation of current field
                $value = array_merge($this->_elements[$name]['validation']['value'], $validation['value']);

                // store the merged result (value)
                $validation['value'] = $value;

            } else {
                // nothing stored previously so just store
                $validation = $element->getValidate();

            }

        } elseif ($type == 'select') {
            // get current validation settings
            $validation = $element->getValidate();

            // get valid options of select-field
            $validOptions = $element->getOptions();

            // make value an array
            $validation['value'] = array();

            // iterate over valid options
            foreach ($validOptions as $key => $option) {
                $validation['value'][] = $option['value'];
            }

        } else {
            // get validation for all other fields (not checkbox, not radio, not select)
            $validation = $element->getValidate();
        }

        // prepare array for returning values/config for current element
        $elementProcessed = array(
            'name'       => $name,
            'type'       => $type,               // store type of element
            'validation' => $validation,         // store validation-config of element
            'html'       => $element->render()
        );

        // and return
        return $elementProcessed;
    }

    /**
     * checks if a validation for an element already exist
     *
     * This method is intend to check if a validation for an element already exist
     *
     * @param string $name The name of the element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean The result of the check TRUE if validation exist, otherwise FALSE
     * @access private
     */
    private function _fieldValidationExist($name)
    {
        return (isset($this->_elements[$name])
                && isset($this->_elements[$name]['validation']['value']));
    }

    /**
     * stores a configurationvariable and its value in configuration-store
     *
     * This method is intend to store an configurationvariable and its value in configuration-store
     *
     * @param string $variable The variable to write
     * @param mixed  $value    The value to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if value was successfully stored, otherwise FALSE
     * @access private
     */
    private function _storeWrite($variable, $value)
    {
        // if not already created
        if (!$this->_config) {
            $this->_config = $this->_storeRead();
        }

        // check which information/config should be store
        $this->_config[$variable] = $value;

        // return status
        return $this->_store->set($this->_getConfigIdentifier(), $this->_config);
    }

    /**
     * Reads a passed variable from store or the complete config if passed variable is null.
     *
     * @param mixed $variable The variable to read from store. If null the whole store is returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING value if $variable was given, otherwise complete store as ARRAY
     * @access private
     */
    private function _storeRead($variable = null)
    {
        // assume empty result
        $result = null;

        // try to retrieve config from session
        $config = $this->_store->get($this->_getConfigIdentifier());

        // check if config exist
        if ($config) {
            // if no variable requested
            if (!$variable) {
                $result = $config;
            } elseif (isset($config[$variable])) {
                $result = $config[$variable];
            }
        }

        // return result
        return $result;
    }

    /**
     * Removes a passed variable and its value from store
     *
     * @param mixed $variable STRING the variable to remove, or NULL to clear the whole store
     *
     * @return boolean TRUE on success, otherwise FALSE
     * @access private
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    private function _storeDelete($variable = null)
    {
        // if not already created
        if (!$this->_config) {
            $this->_config = $this->_storeRead();
        }

        if ($variable) {
            // check which information/config should be removed/deleted
            unset($this->_config[$variable]);
        } else {
            $this->_config = array();
        }

        // store config in session
        return $this->_store->set($this->_getConfigIdentifier(), $this->_config);
    }

    /**
     * returns the configuration-identifier
     *
     * This method is intend to return the configuration-identifier
     *
     * @return string The configuration-identifier
     * @access private
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    private function _getConfigIdentifier()
    {
        // name to use for storing configuration ...
        return self::PREFIX.$this->_name;
    }

    /*******************************************************************************************************************
     * \\ END - HELPER METHODS
     ******************************************************************************************************************/
}

?>
