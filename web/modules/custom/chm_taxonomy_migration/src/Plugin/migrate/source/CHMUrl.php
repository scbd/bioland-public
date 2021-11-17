<?php

namespace Drupal\chm_taxonomy_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\source\Url;

/**
 * Source plugin for retrieving data via URLs.
 *
 * @MigrateSource(
 *   id = "chm_url"
 * )
 */
class CHMUrl extends Url {

  protected $languages = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->languages = \Drupal::languageManager()->getLanguages();
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $return = parent::prepareRow($row);
    if ($return == TRUE) {

      if (!in_array($row->getSourceProperty('langcode'), array_keys($this->languages))) {
        return FALSE;
      }

      $is_default_langcode = !empty($row->getSourceProperty('default_langcode'));
      if ($is_default_langcode == FALSE) {
        // We need to manually set the destination tid because we will end up
        // with one node per translation.
        $sourceIds = $this->currentSourceIds;
        $sourceIds['langcode'] = $row->getSourceProperty('content_translation_source');
        $parentTranslationRow = $this->idMap->getRowBySource($sourceIds);

        reset($this->languages);
        $sourceIds['langcode'] = key($this->languages);
        while (empty($parentTranslationRow['destid1']) && next($this->languages)) {
          $parentTranslationRow = $this->idMap->getRowBySource($sourceIds);
          $sourceIds['langcode'] = key($this->languages);
        }

        if (empty($parentTranslationRow['destid1'])) {
          return FALSE;
        }
        $row->setDestinationProperty('tid', $parentTranslationRow['destid1']);
      }
    }

    return $return;
  }

}
