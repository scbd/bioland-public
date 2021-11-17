<?php

namespace Drupal\chm_common\Plugin\migrate_plus\data_parser\Json;

use Drupal\chm_common\WebsiteUtilities;

/**
 * Obtain JSON data from SCBD API v2013 'index'.
 *
 * @DataParser(
 *   id = "scbd_index_2013_paginated",
 *   title = @Translation("Do paginated requests and parse Solr JSON response")
 * )
 */
class Cbd2013IndexPaginated extends JsonSolrPaginated {

  /**
   * {@inheritdoc}
   */
  protected function getHttpQueryParam() {
    // Add specific parameters here.
    $country = WebsiteUtilities::getWebsiteCountry();
    if (empty($country)) {
      throw new \Exception('Cannot import documents from SCBD: this site has no country set.');
    }
    $iso2l = $country->field_iso_code->value;

    $ret = sprintf('q=realm_ss:chm+AND+schema_s:(nationalReport+nationalReport6)+AND+government_s:%s', strtolower($iso2l));

    return $ret;
  }

}
