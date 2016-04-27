<?php



require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/State/Container.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/State/Interface.php';


class Doozr_Request_Filter extends Doozr_Base_State_Container
{
    protected $url;

    protected $commands = array(
        self::URL_ARGUMENT_FIELDS,
        self::URL_ARGUMENT_SORT,
        self::URL_ARGUMENT_GROUP,
        self::URL_ARGUMENT_PAGINATING_OFFSET,
        self::URL_ARGUMENT_PAGINATING_LIMIT,
    );

    protected $conversionMatrix = array(
        self::URL_FILTER_SORT_ASCENDING  => 'ASC',
        self::URL_FILTER_SORT_DESCENDING => 'DESC',
    );


    const URL_ARGUMENT_FIELDS = 'fields';
    const URL_ARGUMENT_SORT = 'sorting';
    const URL_ARGUMENT_GROUP = 'grouping';
    const URL_ARGUMENT_PAGINATING_OFFSET = 'offset';
    const URL_ARGUMENT_PAGINATING_LIMIT = 'limit';

    const URL_ARGUMENT_ENTRY = '?';
    const URL_ARGUMENT_SEPARATOR = '&';

    const URL_FILTER_SEPARATOR = ',';
    const URL_FILTER_SORT_ASCENDING = '+';
    const URL_FILTER_SORT_DESCENDING = '-';



    public function __construct(Doozr_Base_State_Interface $stateObject, $url = null)
    {
        $this->setStateObject($stateObject);

        if ($url !== null) {
            $this->url = $url;

        } else {
            $this->url = $this->getUrl();

        }

        $this->parse($this->url);
    }


    public function parse($url)
    {
        /* @var $stateObject Doozr_Request_Filter_State */
        $stateObject = $this->getStateObject();

        // Check if URL contains any filterable elements
        if ($this->containsArguments($url) === true) {
            $parts = explode(self::URL_ARGUMENT_ENTRY, $url);
            $path  = $parts[0];
            $query = $parts[1];

            $stateObject->setPath($path);
            $stateObject->setQuery($query);

            // Extract pairs of argument => value
            $pairs = explode('&', $query);

            foreach ($pairs as $pair) {
                $pair = explode('=', $pair);

                $argument = strtolower($pair[0]);
                $value    = $pair[1];

                if ($this->isCommand($argument)) {
                    $stateObject->{$argument}($this->extract($value));

                } else {
                    $stateObject->addArgument($argument, $value);
                }
            }
        }

        return $this;
    }

    protected function extract($value)
    {
        if ($this->containsSeparator($value) === true) {
            $values = explode(self::URL_FILTER_SEPARATOR, $value);

        } else {
            $values = array($value);
        }

        // Check for ASC/DESC if command is grouping
        foreach ($values as $key => $value) {
            if ($this->containsDirection($value)) {
                $values[$key] = array(
                    'value'     => substr($value, 1, strlen($value) - 1),
                    'direction' => $this->convertDirection($value[0]),
                );
            }
        }

        return $values;
    }

    protected function convertDirection($direction)
    {
        return $this->conversionMatrix[$direction];
    }

    protected function containsDirection($string)
    {
        return ($string[0] === self::URL_FILTER_SORT_ASCENDING || $string[0] === self::URL_FILTER_SORT_DESCENDING);
    }

    protected function containsSeparator($string)
    {
        return (stristr($string, self::URL_FILTER_SEPARATOR) !== false);
    }

    protected function containsArguments($string)
    {
        return (stristr($string, self::URL_ARGUMENT_ENTRY) !== false);
    }

    /**
     * @param string $argument
     */
    protected function isCommand($argument)
    {
        return in_array($argument, $this->commands);
    }

    protected function getUrl()
    {
        return $_SERVER['REQUEST_URI'];
    }
}

