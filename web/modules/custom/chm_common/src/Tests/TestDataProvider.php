<?php

namespace Drupal\chm_common\Tests;

use Drupal\taxonomy\Entity\Term;

/**
 * Class TestDataProvider creates test data for the automated tests.
 *
 * @package Drupal\chm_common\Tests
 */
class TestDataProvider {

  public static $COUNTRIES = [
    [
      'name' => 'Romania',
      'field_iso_code' => 'RO',
      'field_iso3l_code' => 'ROU',
      'vid' => 'countries',
    ],
    [
      'name' => 'Canada',
      'field_iso_code' => 'CA',
      'field_iso3l_code' => 'CAN',
      'vid' => 'countries',
    ],
    [
      'name' => 'Albania',
      'field_iso_code' => 'AL',
      'field_iso3l_code' => 'ALB',
      'vid' => 'countries',
    ],
    [
      'name' => 'Kazakhstan',
      'field_iso_code' => 'KZ',
      'field_iso3l_code' => 'KAZ',
      'vid' => 'countries',
    ],
    [
      'name' => 'Belgium',
      'field_iso_code' => 'BE',
      'field_iso3l_code' => 'BEL',
      'vid' => 'countries',
    ],
  ];

  /**
   * Create taxonomy terms for test countries.
   *
   * @return array
   *   Array of terms.
   */
  public static function createCountries() {
    $ret = [];
    foreach (self::$COUNTRIES as $row) {
      $term = Term::create($row);
      $term->save();
      $ret[$term->id()] = $term;
    }
    return $ret;
  }

}
