<?php

namespace Drupal\chm_common\Plugin\migrate\process;

use Drupal\chm_common\WebsiteUtilities;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Migrate the document links coming from CBD API.
 *
 * @MigrateProcessPlugin(
 *   id = "country_from_iso_code",
 *   handle_multiples = FALSE
 * )
 */
class CountryFromIsoCode extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      return [];
    }
    $rows = is_array($value) ? $value : [$value];
    $ret = [];
    foreach ($rows as $row) {
      if ($country = WebsiteUtilities::getCountryByIsoCode($row)) {
        $ret[] = $country->id();
      }
      else {
        $migrate_executable->saveMessage("Could not find country using ISO: $row", MigrationInterface::MESSAGE_ERROR);
      }
    }
    return $ret;
  }

}
