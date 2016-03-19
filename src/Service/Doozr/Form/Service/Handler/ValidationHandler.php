<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service - Handler - ValidationHandler.
 *
 * ValidationHandler.php - Handler for validating a form and all of it parts.
 * This includes validating USER's input by DEVELOPER's rules as well as
 * validating request method, token and some other meta layer stuff. This will
 * makes this class a handler/facade to a subset of validation routines and/or
 * libraries.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
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
 *   must display the following acknowledgment: This product includes software
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Validator/Constant.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Validator/Generic.php';

/**
 * Doozr - Form - Service - Handler - ValidationHandler.
 *
 * Handler for validating a form and all of it parts.
 * This includes validating USER's input by DEVELOPER's rules as well as
 * validating request method, token and some other meta layer stuff. This will
 * makes this class a handler/facade to a subset of validation routines and/or
 * libraries.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Form_Service_Handler_ValidationHandler extends Doozr_Form_Service_Validator_Generic
{
    /**
     * The errors of the form.
     *
     * @var array
     */
    protected $error = [];

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Validates values by validation rules.
     *
     * @param array $values Values to be validated
     * @param array $rules  Rules to be applied on values
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if form valid, otherwise FALSE
     */
    public function validate(array $values, array $rules)
    {
        // Assume no validation and result success
        $result = true;

        if (true !== $valid = $this->validateByRules($rules, $values)) {
            foreach ($valid as $componentName => $error) {
                $this->setError($error, $componentName);
            }

            // Set general form error
            $this->setError(
                [
                    'error' => 'general',
                    'info'  => [],
                ],
                Doozr_Form_Service_Constant::ERROR_IDENTIFIER_FORM
            );

            $result = false;
        }

        return $result;
    }

    /**
     * Returns the error status of an component.
     *
     * @param string $componentName The name of the component to return status for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string $co   mponentName The name of the component
     */
    public function hasError($componentName)
    {
        return true === isset($this->error[$componentName]);
    }

    /**
     * Returns the error of a component.
     *
     * @param string      $componentName The name of the component to return data for
     * @param string|null $default       The default return value as string or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The value of the component if exist, otherwise the $default value
     */
    public function getError($componentName = null, $default = null)
    {
        $result = $default;

        if (null === $componentName) {
            $result = $this->error;
        } else {
            if ($this->hasError($componentName)) {
                $result = $this->error[$componentName];
            }
        }

        return $result;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Sets an error (default for form context).
     *
     * @param string $error         Error as string
     * @param string $componentName Component the error is related to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setError($error, $componentName = Doozr_Form_Service_Constant::ERROR_IDENTIFIER_FORM)
    {
        // Check that each component can have more than one error at once ...
        if (!isset($this->error[$componentName])) {
            $this->error[$componentName] = [];
        }

        $this->error[$componentName][] = $error;
    }

    /**
     * Validates a set of components with configured rules against submitted values.
     * Note: Only submitted values need to be validated cause other values (internally stored)
     * are validated already.
     *
     * @param array $rules  Rules for validation
     * @param array $values Values to apply rules on
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array|bool Error as collection or TRUE on success
     */
    protected function validateByRules(array $rules, array $values)
    {
        // We assume everything valid and result empty ...
        $valid  = true;
        $result = [];

        // Iterate components and get componentName and its configuration
        foreach ($rules as $index => $configuration) {

            // Not every component has a setup for validation(s) ...
            if (true === isset($configuration['validation'])) {
                // Retrieve value ...
                $value            = $values[$index];
                $validationResult = $this->validation($value, $configuration['validation']);

                // Here we: Check if the validationHandler returned error in component
                if (true !== $validationResult) {
                    $valid                     = $valid && false;
                    $validationResult['error'] = Doozr_Form_Service_Validator_Constant::ERROR_PREFIX.
                                                 $validationResult['error'];
                    $result[$index] = $validationResult;
                }
            }
        }

        return (true === $valid) ? $valid : $result;
    }
}
