<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Form
 *
 **********************************************************************************************************************/

/**
 * bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';


/**
 * Instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();


/**
 * Get registry containing DoozR's base object instances
 */
$registry = DoozR_Registry::getInstance();


/**
 * Scenario ->
 *
 * If forms exist, maybe created/designed by a web-designer and you
 * would like to be able to process these forms with DoozR_Form_Service
 * too (e.g. Token, Validation, ...) you will need to extract information
 * for processing out of those existing forms instead of defining it
 * again and keep changes in both directions in sync.
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
 * - $_currentForm
 */
class DoozR_Form_Service_Parser_Formelements
{
    /**
     * The input to parse
     *
     * @var string
     * @access private
     */
    private $_input;

    /**
     * ALL forms as parsed
     */
    private $_forms;
    /**
     * ALL elements in array key = form-id
     */
    private $_elements;
    /**
     * ALL attributes in array by key = (form-id AND field-id)
     */
    private $_properties;

    private $_currentForm;

    /**
     * Contains the template representation of input
     * valid at all time.
     *
     * @var string
     * @access private
     */
    private $_template;

    /**
     * The final output of this parser
     *
     * @var array
     * @access private
     */
    private $_output = array();

    /**
     * The pattern to extract information from HTML-Forms
     *
     * @access public
     * @const
     */
    const REGEXP_PATTERN_FORMS      = '/(<form.*?>)(.*?)<\/form>/ius';
    const REGEXP_PATTERN_ATTRIBUTES = '/[<{1}](\w+)|(\w+)[.*=.*]["|\'](.*?)["|\']/ius';
    const REGEXP_PATTERN_ELEMENTS   = '/(<(input|select|textarea)(.*?)>)/ius';

    const TEMPLATE_PREFIX           = 'DOOZR_FORM_SERVICE_TEMPLATE_';
    const TEMPLATE_IDENTIFIER_FORM  = 'FORM';
    const TEMPLATE_BRACKETS_OPEN    = '{{';
    const TEMPLATE_BRACKETS_CLOSE   = '}}';


    /**
     * Constructor of the class
     *
     * @param string $html The HTML code to parse form from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct($html = '')
    {
        if ($html !== '') {
            $this->_input = $html;
        }
    }

    /**
     * Parses one or more forms out of input HTML
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the parser result (configuration of forms)
     * @access public
     */
    public function parse($html = '')
    {
        if ($html !== '') {
            $this->_input = $html;

        } else {
            if ($this->_input === null) {
                throw new Exception('Please provide HTML to parse information from first!');
            }
        }

        // assume empty result
        $result = array();

        // initial template is 1:1 copy
        $this->_template = $this->_input;

        // extract form(s) HTML
        $this->_forms = $this->parseFormsFromHtml($this->_input);

        // build configuration from extracted form(s) HTML
        $configuration = $this->parseConfigurations($this->_forms);

        // @TODO: remove asap -> just for better readability
        $result = $configuration;

        // return the parsed data
        return $result;
    }

    /**
     * Parses configuration-sets out of passed form-HTML.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
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
            $properties = $this->readPropertiesFromHtml($forms[1][$i]);

            // build new structure
            $configuration = $this->prepareStructureAndData($properties);

            // get Id
            $id = $this->getId($configuration['properties']);

            // store info about current processed form
            $this->_currentForm = $id;

            // inject Id
            $configuration['properties']['id'] = $id;

            // build identifier for template
            $templateIdentifier = $this->buildTemplateIdentifier($id);

            // SO we know which form is processed so insert placeholder for this
            // form into input html and store it as template
            $this->_template = str_replace(
                $forms[1][$i],        // lookup  @was $forms[0][$i]
                $templateIdentifier,  // replace
                $this->_template      // subject
            );

            // store in result
            $setup = array(
                'form'     => $configuration['properties'],
                'elements' => array()
            );

            // now parse elements from inside <form>
            $elements = $this->readFormTagsFromHtml($forms[2][$i]);

            // now parse element config
            $setup['elements'] = $this->parseElements($elements);

            $result[] = $setup;
        }

        return $result;
    }

    protected function buildTemplateIdentifier($id, $type = self::TEMPLATE_IDENTIFIER_FORM)
    {
        $type   = strtoupper($type);
        $result = self::TEMPLATE_BRACKETS_OPEN . self::TEMPLATE_PREFIX . $type . '-' . $id . self::TEMPLATE_BRACKETS_CLOSE;
        return $result;
    }

    /**
     * Parses elements from passed array of elements-HTML.
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
            $element = $this->readPropertiesFromHtml($elements[1][$j]);

            // rearrange them
            $element = $this->prepareStructureAndData($element);

            // check fieldname for existence of our own prefix so rip off
            $fieldName = (isset($element['properties']['name'])) ?
            substr($element['properties']['name'], 0, strlen(DoozR_Form_Service::PREFIX)) :
            '';

            if ($fieldName !== DoozR_Form_Service::PREFIX) {
                // get Id
                $id = $this->getId($element['properties']);

                // inject Id
                $element['properties']['id'] = $id;

                // get template identifier
                $templateIdentifier = $this->buildTemplateIdentifier($id, $element['properties']['type']);

                $this->_template = str_replace(
                    $elements[1][$j],     // lookup
                    $templateIdentifier,  // replace
                    $this->_template      // subject
                );

                // drop into result
                $result[] = $element;
            }
        }

        return $result;
    }

    /**
     * Returns either existing Id from passed data or calculates
     * a new one based on input.
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
            pre($data);
            $result = sha1($data['action'].$data['method']);
        }

        return $result;
    }

    /**
     * Takes an array of extracted properties and prepare it's
     * structure and data for further use.
     *
     * @param array $data The data to re-arrange
     *
     * @return array The array with the resulting new structure
     * @throws Exception
     */
    protected function prepareStructureAndData(array $data)
    {
        // check passed input (simple)
        if (!isset($data[1]) || !isset($data[2]) || !isset($data[3])) {
            throw new Exception('Array passed to '.__METHOD__.' could not be processed!');
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
            'tag' => $tag,
            'properties' => $properties
        );

        return $result;
    }


    /**
     * MOVE TO ABSTRACT
     */


    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * This method extracts all properties from HTML tags from passed
     * HTML. It repeats till all properties in passed HTML are extracted.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the result
     * @access protected
     */
    protected function readPropertiesFromHtml($html)
    {
        // assume empty result
        $result = array();

        preg_match_all(self::REGEXP_PATTERN_ATTRIBUTES, $html, $result);

        // return properties extracted as array
        return $result;
    }

    /**
     * This method extracts all HTML tags from passed HTML.
     * It repeats till all tags in passed HTML are extracted.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the result
     * @access protected
     */
    protected function readFormTagsFromHtml($html)
    {
        // assume empty result
        $result = array();

        preg_match_all(self::REGEXP_PATTERN_ELEMENTS, $html, $result);

        // return form elements extracted as array
        return $result;
    }

    /**
     * This method extracts all form HTML from "<form ..." to "</form>"
     * It repeats till all forms in passed HTML are extracted.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the result
     * @access protected
     */
    protected function parseFormsFromHtml($html)
    {
        // assume empty result
        $result = array();

        preg_match_all(self::REGEXP_PATTERN_FORMS, $html, $result);

        // return form elements extracted as array
        return $result;
    }
}

// dummy call
$html   = file_get_contents('form.html');
$parser = new DoozR_Form_Service_Parser_Formelements();
$config = $parser->parse($html);

// show template generated while extracting info
pre($parser->getTemplate());

// show config retrieved
pred($config);
