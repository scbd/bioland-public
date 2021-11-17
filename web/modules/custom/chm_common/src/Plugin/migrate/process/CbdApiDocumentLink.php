<?php

namespace Drupal\chm_common\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Migrate the document links coming from CBD API.
 *
 * @MigrateProcessPlugin(
 *   id = "cbd_api_document_link",
 *   handle_multiples = FALSE
 * )
 */
class CbdApiDocumentLink extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($values, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $ret = [];
    if (!empty($values)) {
      $values = json_decode($values, TRUE);
      foreach ($values as $value) {
        // Choose just English PDF and DOC files - TODO: Multilingual.
        if (preg_match('/-en.doc$|-en.pdf$/i', $value['url'])
            && preg_match('/-en.doc$|-en.pdf$/i', $value['name'])) {
          $filename = $value['name'];
          $url = $value['url'];
          $ret[$url] = $filename;
        }
      }
    }
    return $ret;
  }

}
