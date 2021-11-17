<?php

namespace Drupal\chm_common\Plugin\migrate_plus\data_parser\Json;

use Drupal\chm_common\WebsiteUtilities;

/**
 * Obtain National Targets as JSON data from SCBD API v2013 'index'.
 *
 * @DataParser(
 *   id = "scbd_2013_national_targets",
 *   title = @Translation("Get National Targets from SCBD API")
 * )
 */
class Cbd2013NationalTargets extends JsonSolrPaginated {

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

    $ret = 'sort=title_s%20asc&fl=title_s,url_ss,id,description_s,aichiTargets_ss&fq=realm_ss:chm&fq=schema_s:nationalTarget&q=government_s:'. strtolower($iso2l);

    #$ret = sprintf('q=realm_ss:chm+AND+schema_s:nationalReport+AND+government_s:%s', strtolower($iso2l));

    return $ret;
  }

}
