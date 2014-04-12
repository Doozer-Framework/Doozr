<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Html.php - Parser for parsing form an configuration out of existing HTML
 * Code. Assume the following scenario ->
 * If forms exist, maybe created/designed by a web-designer and you would like
 * to be able to process these forms with DoozR_Form_Service too (e.g. Token,
 * Validation, ...) you will need to extract information for processing out
 * of those existing forms instead of defining it again and keep changes in
 * both directions in sync.
 *
 * This class parses information about form(s) and it fields and provide
 * access to it. The parsed information is returned as an array.
 *
 * @TODO:
 *
 * - parse <option></option> out of <select>-element(s) to retrieve
 *   values and so on for rebuilding the form.
 *
 * - parse innerHTML out of <textarea></textarea>
 *
 * - parse ALL HTML-tags to be able to rebuild the whole structure?
 *   or just replace the parsec elements with a placeholder like
 *   {{DOOZR_FORM_SERVICE_ELEMENT_123...789}} or something like
 *   that. So the parser would still parse elements like before, but
 *   will use the resulting "template" for ruther rebuilding the
 *   form.
 *
 * - Use concrete class instead of array for config structure!
 *   Maybe something like: DoozR_Form_Service_Configuration
 *   will result in an configuration-object instance :)
 *   do not forget to TYPEHINT!
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Parser/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Parser/Interface.php';

/**
 * DoozR - Form - Service
 *
 * Parser for parsing HTML files for forms which cab be used in form-manager
 * as preconfigured templates.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: 1273acd716766791d2770bfe0bd9f1d161a7d047 $
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Form_Service_Parser_Html extends DoozR_Form_Service_Parser_Abstract
    implements
    DoozR_Form_Service_Parser_Interface
{
    /**
     * Contains the template representation of input
     * valid at all time.
     *
     * @var string
     * @access private
     */
    protected $template;

    /**
     * Parsed forms
     *
     * @var array
     * @access protected
     */
    protected $forms = array();

    /**
     * The current active configuration
     *
     * @var array
     * @access protected
     */
    #protected $configuration;

    protected $configurations;

    /**
     * The pattern to extract HTML-Forms
     *
     * @access public
     * @const
     */
    const REGEXP_PATTERN_FORMS = '/(<form.*?>)(.*?)<\/form>/ius';

    /**
     * The pattern to extract HTML elements attributes
     *
     * @access public
     * @const
     */
    const REGEXP_PATTERN_ATTRIBUTES = '/<([\w]*)|\s*([\w-]*)\s*=\s*[\'"](.*?)[\'"]/ius';

    /**
     * The pattern to exract special elements like:
     * INPUT, SELECT, TEXTAREA
     *
     * @access public
     * @const
     */
    const REGEXP_PATTERN_ELEMENTS = '/(<(input|select|textarea)(.*?)>)/ius';

    const REGEXP_PATTERN_CONTAINER_ELEMENTS = '';

    /**
     * The prefix for templae var placeholder(s)
     *
     * @access public
     * @const
     */
    const TEMPLATE_PREFIX = 'DOOZR_FORM_SERVICE_TEMPLATE_';

    /**
     * The template identifier for form(s)
     *
     * @access public
     * @const
     */
    const TEMPLATE_IDENTIFIER_FORM = 'FORM';

    /**
     * Template double mustache var:
     * START
     *
     * @access public
     * @const
     */
    const TEMPLATE_BRACKETS_OPEN = '{{';

    /**
     * Template double mustache var:
     * END
     *
     * @access public
     * @const
     */
    const TEMPLATE_BRACKETS_CLOSE = '}}';


    /*-----------------------------------------------------------------------------------------------------------------*
    | Public Chaining API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Parses form(s) and DoozR-Form-Service directives to configuration from previously set HTML.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Parser_Interface Instance for chaining
     * @access public
     * @throws \Exception
     */
    public function parse()
    {
        // Get input
        $input = $this->getInput();

        // So empty buffer is not an error its an exception
        if ($input === null) {
            throw new Exception(
                'Please provide HTML to parse information from first!'
            );
        }

        // Initial template is 1:1 copy
        $this->setTemplate($input);

        // Extract form(s) HTML
        $this->setForms($this->parseFormsFromHtml($input));

        // Build configuration from extracted form(s) HTML
        $configuration = $this->getConfiguration();

        // forms
        $forms = $this->parseConfigurations($this->getForms());

        foreach ($forms as $form) {
            $temp = clone $configuration;
            $temp->parseFromArray($form);
            $this->addConfiguration($temp);
        }

        // return instance for chaining
        return $this;
    }

    /*-----------------------------------------------------------------------------------------------------------------*
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    public function setForms(array $forms)
    {
        $this->forms = $forms;
    }

    public function addConfiguration(DoozR_Form_Service_Configuration $configuration)
    {
        $this->configurations[$configuration->getId()] = $configuration;
    }

    public function getConfigurations()
    {
        return $this->configurations;
    }

    public function getForms()
    {
        return $this->forms;
    }

    public function setForm($id, DoozR_Form_Service_Configuration $configuration)
    {
        $this->forms[$id] = $configuration;
    }

    public function getForm($id)
    {
        return (isset($this->forms[$id])) ? $this->forms[$id] : null;
    }

    /**
     * Setter for template.
     *
     * @param mixed $template The template to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Getter for template.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The stored template if set, otherwise NULL
     * @access public
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /*-----------------------------------------------------------------------------------------------------------------*
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Parses configuration-sets out of passed form-HTML.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @param array $forms
     *
     * @return array The resulting structure
     * @access protected
     */
    protected function parseConfigurations(array $forms)
    {
        // get count of elements
        $countForms = count($forms[0]);

        // iterate single form(s) and prepare data
        for ($i = 0; $i < $countForms; ++$i) {

            // extract properties of form
            $properties = $this->parsePropertiesFromHtml($forms[1][$i]);

            // build new structure
            $configuration = $this->prepareStructureAndData($properties);

            // get Id
            $id = $this->getId($configuration['properties']);

            // store info about current processed form
            #$this->_currentForm = $id;

            // inject Id
            $configuration['properties']['id'] = $id;

            // build identifier for template
            $templateIdentifier = $this->buildTemplateIdentifier($id);

            // SO we know which form is processed so insert placeholder for this
            // form into input html and store it as template
            $this->template = str_replace(
                $forms[1][$i],        // lookup  @was $forms[0][$i]
                $templateIdentifier,  // replace
                $this->template       // subject
            );

            // store in result
            $setup = array(
                'form'     => array(array('tag' => 'form', 'properties' => $configuration['properties'])),
                'elements' => array()
            );

            // now parse elements from inside <form>
            $elements = $this->parseFormTagsFromHtml($forms[2][$i]);

            // now parse element config
            $setup['elements'] = $this->parseElements($elements);

            $result[] = $setup;
        }

        return $result;
    }

    /**
     * Builds template identifier by passed id and type.
     *
     * @param string $id   The id of the element
     * @param string $type The type of the element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The template identifier
     * @access protected
     */
    protected function buildTemplateIdentifier($id, $type = self::TEMPLATE_IDENTIFIER_FORM)
    {
        $type = strtoupper($type);

        return self::TEMPLATE_BRACKETS_OPEN .
        self::TEMPLATE_PREFIX .
        $type .
        '-' .
        $id .
        self::TEMPLATE_BRACKETS_CLOSE;
    }

    /**
     * Parses elements from passed array of elements-HTML.
     *
     * @param array $elements The elements to use ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The resulting structure
     * @access protected
     */
    protected function parseElements(array $elements)
    {
        // assume empty result
        $result = array();

        //@TODO: check input?

        // get count of inner elements
        $countElements = count($elements[1]);

        // iterate elements of <form>
        for ($j = 0; $j < $countElements; ++$j) {
            // get properties
            $element = $this->parsePropertiesFromHtml($elements[1][$j]);

            // rearrange them
            $element = $this->prepareStructureAndData($element);

            // check fieldname for existence of our own prefix so rip off
            $fieldName = (isset($element['properties']['name'])) ?
                substr($element['properties']['name'], 0, strlen(DoozR_Form_Service_Constant::PREFIX)) :
                '';

            if ($fieldName !== DoozR_Form_Service_Constant::PREFIX) {
                // get Id
                $id = $this->getId($element['properties']);

                // inject Id
                $element['properties']['id'] = $id;

                // get template identifier
                $templateIdentifier = $this->buildTemplateIdentifier(
                    $id,
                    isset($element['properties']['type']) ? $element['properties']['type'] : $element['tag']
                );

                $this->template = str_replace(
                    $elements[1][$j],     // lookup
                    $templateIdentifier,  // replace
                    $this->template       // subject
                );

                // drop into result
                $result[] = $element;
            }
        }

        return $result;
    }

    /**
     * Returns either existing Id from passed data or calculates a new one based on input.
     *
     * @param array $data The data used for generating/returning id
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The (new) Id
     * @access protected
     */
    protected function getId(array $data)
    {
        $result = null;

        if (isset($data['id'])) {
            $result = $data['id'];

        } elseif (isset($data['name'])) {
            $result = $data['name'];

        } else {
            #$result = sha1($data['action'] . $data['method']);
            $result = sha1(serialize($data));
        }

        return $result;
    }

    /**
     * Takes an array of extracted properties and prepare it's structure and data for further use.
     *
     * @param array $data The data to re-arrange
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The array with the resulting new structure
     * @access protected
     * @throws Exception
     */
    protected function prepareStructureAndData(array $data)
    {
        // check passed input (simple)
        if (!isset($data[1]) || !isset($data[2]) || !isset($data[3])) {
            throw new Exception('Array passed to ' . __METHOD__ . ' could not be processed!');
        }

        // extract required information
        $tag = ($data[1][0] !== '') ? $data[1][0] : null;

        if ($tag !== null) {
            $keys   = array_slice($data[2], 1);
            $values = array_slice($data[3], 1);
        } else {
            $keys   = $data[2];
            $values = $data[3];
        }

        // get result combined and keys in lower case
        $properties = array_change_key_case(array_combine($keys, $values), CASE_LOWER);

        $result = array(
            'tag'        => $tag,
            'properties' => $properties
        );

        return $result;
    }

    /**
     * This method extracts all properties from HTML tags from passed
     * HTML. It repeats till all properties in passed HTML are extracted.
     *
     * @param string The HTML to parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the result
     * @access protected
     */
    protected function parsePropertiesFromHtml($html)
    {
        return $this->parseGeneric(self::REGEXP_PATTERN_ATTRIBUTES, $html);
    }

    /**
     * This method extracts all HTML tags from passed HTML.
     * It repeats till all tags in passed HTML are extracted.
     *
     * @param string $html The HTML to parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the result
     * @access protected
     */
    protected function parseFormTagsFromHtml($html)
    {
        return $this->parseGeneric(self::REGEXP_PATTERN_ELEMENTS, $html);
    }

    /**
     * This method extracts all form HTML from "<form ..." to "</form>"
     * It repeats till all forms in passed HTML are extracted.
     *
     * @param string $html The HTML to parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the result
     * @access protected
     */
    protected function parseFormsFromHtml($html)
    {
        return $this->parseGeneric(self::REGEXP_PATTERN_FORMS, $html);
    }

    /**
     * Generic parser which parses all matches for pattern passed in buffer passed.
     *
     * @param string $pattern The pattern to use
     * @param string $buffer  The buffer to parse from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the result
     * @access protected
     */
    protected function parseGeneric($pattern, $buffer = '')
    {
        // assume empty result
        $result = array();

        preg_match_all($pattern, $buffer, $result);

        // return form elements extracted as array
        return $result;
    }
}
