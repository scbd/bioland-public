<?php

namespace Drupal\chm_common;

use Drupal\taxonomy\Entity\Term;

/**
 * Class WebsiteUtilities contains commonly shared utilities across website.
 *
 * @package Drupal\chm_common
 */
class WebsiteUtilities {

  /**
   * Countries taxonomy machine name.
   */
  const TAXONOMY_COUNTRIES = 'countries';

  /**
   * Get the list of countries keyed by ISO 3L code.
   *
   * @return array
   *   Array of country names keyed by ISO 3L code.
   */
  public static function getCountryListKeyedByIso3() {
    $ret = [];
    $rows = self::getCountryList();
    foreach ($rows as $row) {
      $ret[strtoupper($row->field_iso3l_code->value)] = $row->getName();
    }
    return $ret;
  }

  /**
   * Retrieve the list of taxonomy terms from countries taxonomy.
   *
   * @param string $sort
   *   Sort ordering, by default by country name.
   *
   * @return array|Term
   *   Array of taxonomy terms representing countries.
   */
  public static function getCountryList($sort = 'name') {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', self::TAXONOMY_COUNTRIES);
    $query->sort($sort);
    $rows = $query->execute();
    return Term::loadMultiple($rows);
  }

  /**
   * Get the country for this website, if linked to a country (national portal).
   *
   * @return \Drupal\taxonomy\Entity\Term|null
   *   Taxonomy term for this country or null if not a national portal.
   */
  public static function getWebsiteCountry() {
    if ($iso = \Drupal::config('system.site')->get('chm_government')) {
      return self::getCountryByIsoCode($iso);
    }
    return NULL;
  }

  /**
   * Retrieve the country taxonomy term by ISO code (2 or 3 letter).
   *
   * @param string $iso
   *   Country iso-2l or iso-3l code.
   *
   * @return \Drupal\taxonomy\Entity\Term|null
   *   Taxonomy term or null if no match was found.
   */
  public static function getCountryByIsoCode($iso) {
    if (empty($iso)) {
      return NULL;
    }
    $iso = trim($iso);
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', self::TAXONOMY_COUNTRIES);
    if (strlen($iso) == 3) {
      $query->condition('field_iso3l_code', $iso);
    }
    else {
      $query->condition('field_iso_code', $iso);
    }
    $rows = $query->execute();
    if (!empty($rows)) {
      return Term::load(reset($rows));
    }
    return NULL;
  }

}
