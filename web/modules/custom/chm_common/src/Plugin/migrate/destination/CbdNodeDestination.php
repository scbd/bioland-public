<?php

namespace Drupal\chm_common\Plugin\migrate\destination;

use Drupal\migrate\Plugin\migrate\destination\EntityConfigBase;

/**
 * Fix files on rollback.
 *
 * @MigrateDestination(
 *   id = "cbd:node"
 * )
 */
class CbdNodeDestination extends EntityConfigBase {

  /**
   * {@inheritdoc}
   */
  public function rollback(array $destination_identifier) {
    parent::rollback($destination_identifier);
    // TODO: Get files from all fields and mark them as temporary.
    // See https://www.drupal.org/node/2891902 (and 2708411, 2810355, 2821423).
  }

  /**
   * {@inheritdoc}
   */
  protected static function getEntityTypeId($plugin_id) {
    return 'node';
  }

}
