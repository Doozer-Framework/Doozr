<?php

require_once '../../../../DoozR/Bootstrap.php';


class ServiceTest extends PHPUnit_Framework_TestCase
{
    /* @var DoozR_I18n_Service $service */
    protected static $service;
    protected static $core;

    protected static $defaults = array(
        'locale' => 'en'
    );

    protected static $locale = 'de-de';

    protected static $testLocale = 'de';

    protected static $invalidLocale = 'de-11111de-de-de';

    protected static $formatter = array(
        'Currency',
        'Datetime',
        'Measure',
        'Number',
        'String'
    );


    public function setUp()
    {
        // init the inner core ;)
        $core = DoozR_Core::getInstance();

        // get registry
        $registry = DoozR_Registry::getInstance();

        // load I18n service
        self::$service = DoozR_Loader_Serviceloader::load('i18n', $registry->config);
    }

    public function testLoadable()
    {
        $this->assertInstanceOf('DoozR_I18n_Service', self::$service);
    }

    /**
     *
     */
    public function testDefaultLocale()
    {
        $this->assertEquals(self::$defaults['locale'], self::$service->getActiveLocale());
    }

    public function testSetLocale()
    {
        self::$service->setActiveLocale(self::$locale);
        $this->assertEquals(self::$locale, self::$service->getActiveLocale());
    }

    /**
     * @expectedException DoozR_I18n_Service_Exception
     */
    public function testSetInvalidLocale()
    {
        self::$service->setActiveLocale(self::$invalidLocale);
    }


    public function testDetector()
    {
        $this->assertInstanceOf('DoozR_I18n_Service_Detector', self::$service->getDetector());
    }

    public function testGetClientPreferedLocale()
    {
        $this->assertEquals('de-de', self::$service->getClientPreferedLocale());
    }

    public function testGetCurrencyFormatter()
    {
        self::$service->setActiveLocale(self::$testLocale);

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Format_Currency',
            self::$service->getFormatter('Currency')
        );
    }

    public function testGetDatetimeFormatter()
    {
        self::$service->setActiveLocale(self::$testLocale);

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Format_Datetime',
            self::$service->getFormatter('Datetime')
        );
    }

    public function testGetMeasureFormatter()
    {
        self::$service->setActiveLocale(self::$testLocale);

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Format_Measure',
            self::$service->getFormatter('Measure')
        );
    }

    public function testGetNumberFormatter()
    {
        self::$service->setActiveLocale(self::$testLocale);

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Format_Number',
            self::$service->getFormatter('Number')
        );
    }

    public function testGetStringFormatter()
    {
        self::$service->setActiveLocale(self::$testLocale);

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Format_String',
            self::$service->getFormatter('String')
        );
    }

    public function testGetTranslator()
    {
        self::$service->setActiveLocale(self::$testLocale);

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Translator',
            self::$service->getTranslator()
        );
    }

    public function testTranslate()
    {
        self::$service->setActiveLocale(self::$testLocale);
        $translator = self::$service->getTranslator();
        $translator->setNamespace('default');
        $this->assertEquals('Das ist "bar".', $translator->_('This is "bar".'));
    }

    public function tearDown()
    {
        self::$service = null;
    }
}
