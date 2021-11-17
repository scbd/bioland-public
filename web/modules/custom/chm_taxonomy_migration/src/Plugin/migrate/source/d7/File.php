<?php

namespace Drupal\chm_taxonomy_migration\Plugin\migrate\source\d7;

use Drupal\Core\Database\Query\Condition;
use Drupal\file\Plugin\migrate\source\d7\File as D7File;

/**
 * Drupal 7 file source from database.
 *
 * @MigrateSource(
 *   id = "chm_d7_file"
 * )
 */
class File extends D7File {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();

    // Filter by folder.
    if (isset($this->configuration['source_folder'])) {
      // Accept either a single folder, or a list.
      $conditions = new Condition('OR');
      foreach ((array) $this->configuration['source_folder'] as $folder) {
        $conditions->condition('uri', '%://' . $folder . '%', 'LIKE');
      }
      $query->condition($conditions);
    }

    return $query;
  }

}
