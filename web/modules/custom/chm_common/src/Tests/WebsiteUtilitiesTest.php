<?php
/**
 * @codingStandardsIgnoreFile
 */
namespace Drupal\chm_common\Tests;

use Drupal\chm_common\WebsiteUtilities;
use Drupal\simpletest\WebTestBase;

/**
 * Class WebsiteUtilitiesTest tests the WebsiteUtilities class.
 *
 * @group chm
 */
class WebsiteUtilitiesTest extends WebTestBase {

  public static $modules = ['chm_tests_helper'];

  private $countries;

  protected function setUp() {
    $this->strictConfigSchema = FALSE;
    parent::setUp();
    \Drupal::entityDefinitionUpdateManager()->applyUpdates();

    $this->countries = TestDataProvider::createCountries();
    \Drupal::configFactory()->getEditable('system.site')->set('chm_government', 'ROU')->save();
  }

  public function testGetCountryByIsoCode() {
    $c = WebsiteUtilities::getCountryByIsoCode('RO');
    $this->assertTrue('Romania', $c->getName());
    $c = WebsiteUtilities::getCountryByIsoCode('ro');
    $this->assertTrue('Romania', $c->getName());
    $c = WebsiteUtilities::getCountryByIsoCode('CAN');
    $this->assertTrue('Canada', $c->getName());
    $c = WebsiteUtilities::getCountryByIsoCode('can');
    $this->assertTrue('Canada', $c->getName());
    $c = WebsiteUtilities::getCountryByIsoCode('NULL');
    $this->assertTrue(empty($c), 'Country NULL does not exists');
    $c = WebsiteUtilities::getCountryByIsoCode('C');
    $this->assertTrue(empty($c), 'Country C does not exists');
  }

  public function testGetWebsiteCountry() {
    $c = WebsiteUtilities::getWebsiteCountry();
    $this->assertTrue(!empty($c), 'Website has country set');
    $this->assertEqual(1, $c->id(), 'Website country TID is 1');
  }

  public function testGetCountryList() {
    $c = WebsiteUtilities::getCountryList();
    $this->assertEqual(count(TestDataProvider::$COUNTRIES), count($c), 'We have all countries');
    // Check sorting by name.
    $row = array_shift($c);
    $this->assertEqual('Albania', $row->getName());
    $row = array_shift($c);
    $this->assertEqual('Belgium', $row->getName());
    $row = array_shift($c);
    $this->assertEqual('Canada', $row->getName());
    $row = array_shift($c);
    $this->assertEqual('Kazakhstan', $row->getName());
    $row = array_shift($c);
    $this->assertEqual('Romania', $row->getName());
  }

  public function testGetCountryListKeyedByIso3() {
    $c = WebsiteUtilities::getCountryListKeyedByIso3();
    $this->assertEqual('Albania', $c['ALB']);
    $this->assertEqual('Belgium', $c['BEL']);
    $this->assertEqual('Canada', $c['CAN']);
    $this->assertEqual('Kazakhstan', $c['KAZ']);
    $this->assertEqual('Romania', $c['ROU']);
  }

}
