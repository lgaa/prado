<?php


use Prado\I18N\core\CultureInfo;


/**
 * @package System.I18N.core
 */
class CultureInfoTest extends PHPUnit_Framework_TestCase {
  protected $culture;

  function setUp() {
    $this->culture = CultureInfo::getInvariantCulture();
  }

  function testCultureName() {
    $name = 'en';

    $this->assertEquals($name, $this->culture->Name);

    //the default/invariant culture should be neutral
    $this->assertTrue($this->culture->IsNeutralCulture);
  }

  function testCultureList() {
    $allCultures = CultureInfo::getCultures();
    $neutralCultures = CultureInfo::getCultures(CultureInfo::NEUTRAL);
    $specificCultures = CultureInfo::getCultures(CultureInfo::SPECIFIC);

    //there should be 246 cultures all together.
    $this->assertEquals(count($allCultures),722);
    $this->assertEquals(count($neutralCultures),122);
    $this->assertEquals(count($specificCultures),600);
  }

  function testParentCultures() {
    $zh_CN = new CultureInfo('zh_CN');
    $parent = $zh_CN->Parent;
    $grandparent = $parent->Parent;

    $this->assertEquals($zh_CN->Name, 'zh_CN');
    $this->assertEquals($parent->Name, 'zh');
    $this->assertEquals($grandparent->Name, 'en');
    $this->assertEquals($grandparent->Parent->Name, 'en');
  }

  function testCountryNames() {
    $culture = new CultureInfo('fr_FR');
    $this->assertEquals($culture->Countries['AE'], 'Émirats arabes unis');
  }

  function testCurrencies() {
    $culture = new CultureInfo('en_AU');
    $au = array('$', 'Australian Dollar');
    $this->assertEquals($au, $culture->Currencies['AUD']);
  }

  function testLanguages() {
    $culture = new CultureInfo('fr_BE');
    $this->assertEquals($culture->Languages['fr'], 'français');
  }

  function testScripts() {
    $culture = new CultureInfo('fr');
    $this->assertEquals($culture->Scripts['Armn'], 'arménien');
  }

  function testTimeZones() {
    $culture = new CultureInfo('it');
    $zone = "Africa/Abidjan";
    $this->assertEquals($culture->TimeZones[0], $zone);
  }

  function test_missing_english_names_returns_culture_code()
  {
    $culture = new CultureInfo('iw');
    $this->assertEquals($culture->getEnglishName(), 'iw');
  }
}
