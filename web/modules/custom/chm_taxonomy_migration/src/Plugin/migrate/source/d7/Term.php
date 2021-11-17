<?php

namespace Drupal\chm_taxonomy_migration\Plugin\migrate\source\d7;

use Drupal\Core\Language\LanguageInterface;
use Drupal\migrate\Row;
use Drupal\taxonomy\Plugin\migrate\source\d7\Term as TaxonomyD7Term;

/**
 * Taxonomy term source from database.
 *
 * @MigrateSource(
 *   id = "chm_d7_taxonomy_term"
 * )
 */
class Term extends TaxonomyD7Term {

  /**
   * {@inheritdoc}
   */
  public function count($refresh = FALSE) {
    if (!empty($this->configuration['translations'])) {
      /** @var \Drupal\Core\Language\LanguageManager $languageManager */
      $languageManager = \Drupal::languageManager();
      $ids = $this->query()->fields('td', ['tid'])->execute()->fetchCol();
      $translations = $this->select('entity_translation', 't')
        ->fields('t', ['language'])
        ->condition('entity_type', 'taxonomy_term')
        ->condition('language', array_keys($languageManager->getLanguages()), 'IN')
        ->condition('entity_id', $ids, 'IN')
        ->execute()->fetchCol();
      return count($translations);
    }
    else {
      return $this->query()->countQuery()->execute()->fetchField();
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeIterator() {
    $data = iterator_to_array(parent::initializeIterator());
    if (!empty($this->configuration['translations'])) {
      /** @var \Drupal\Core\Language\LanguageManager $languageManager */
      $languageManager = \Drupal::languageManager();
      $translatedData = [];
      foreach ($data as $item) {
        $translatedData[] = $item;
        $translations = $this->select('entity_translation', 't')
          ->fields('t', ['language'])
          ->condition('entity_type', 'taxonomy_term')
          ->condition('language', array_keys($languageManager->getLanguages()), 'IN')
          ->condition('entity_id', $item['tid'])
          ->execute()->fetchCol();
        if (!empty($translations) && count($translations) > 1) {
          foreach ($translations as $langCode) {
            if ($langCode != $item['language']) {
              $translatedItem = $item;
              $translatedItem['parent_translation'] = $item['language'];
              $translatedItem['language'] = $langCode;
              $translatedData[] = $translatedItem;
            }
          }
        }
      }
      $data = $translatedData;
    }
    return new \IteratorIterator(new \ArrayObject($data));
  }

  /**
   * Retrieve the fields found on source.
   *
   * @param \Drupal\migrate\Row $row
   *   The row object.
   *
   * @return array[]
   *   The field instances, keyed by field name.
   */
  protected function getSourceFields(Row $row) {
    return $this->getFields('taxonomy_term', $row->getSourceProperty('machine_name'));
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $return = parent::prepareRow($row);
    if ($return == TRUE) {
      /** @var \Drupal\Core\Language\LanguageManager $languageManager */
      $languageManager = \Drupal::languageManager();
      $langCode = $row->getSourceProperty('language');
      if (!array_key_exists($langCode, $languageManager->getLanguages())) {
        return FALSE;
      }
      $source_fields = $this->getSourceFields($row);
      foreach (array_keys($source_fields) as $field) {
        $tid = $row->getSourceProperty('tid');
        $value = $this->getFieldValues('taxonomy_term', $field, $tid);
        if (is_array($value) && !is_numeric(key($value))) {
          // Translations are available.
          if (array_key_exists($langCode, $value)) {
            $value = $value[$langCode];
          }
          elseif (array_key_exists(LanguageInterface::LANGCODE_NOT_SPECIFIED, $value)) {
            $value = $value[LanguageInterface::LANGCODE_NOT_SPECIFIED];
          }
          else {
            $value = NULL;
          }
        }
        $row->setSourceProperty($field, $value);
      }

      if (!empty($row->getSourceProperty('parent_translation'))) {
        // We need to manually set the destination tid because we will end up
        // with one node per translation.
        $sourceIds = $this->currentSourceIds;
        $sourceIds['language'] = $row->getSourceProperty('parent_translation');
        $parentTranslationRow = $this->idMap->getRowBySource($sourceIds);
        if (empty($parentTranslationRow['destid1'])) {
          return FALSE;
        }
        $row->setDestinationProperty('tid', $parentTranslationRow['destid1']);
      }

      $parent = $row->getSourceProperty('parent');
      if (empty($parent)) {
        $parent = 0;
      }
      elseif (is_array($parent)) {
        $parent = reset($parent);
      }
      $row->setSourceProperty('parent', $parent);
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  protected function getFieldValues($entity_type, $field, $entity_id, $revision_id = NULL, $language = NULL) {
    $table = (isset($revision_id) ? 'field_revision_' : 'field_data_') . $field;
    $query = $this->select($table, 't')
      ->fields('t')
      ->condition('entity_type', $entity_type)
      ->condition('entity_id', $entity_id)
      ->condition('deleted', 0);
    if (isset($revision_id)) {
      $query->condition('revision_id', $revision_id);
    }
    $values = [];
    foreach ($query->execute() as $row) {
      $current_value = NULL;
      foreach ($row as $key => $value) {
        if (strpos($key, $field) === 0) {
          $column = substr($key, strlen($field) + 1);
          $current_value[$column] = $value;
        }
      }
      if (!empty($current_value)) {
        if (!empty($row['language'])) {
          $values[$row['language']][$row['delta']] = $current_value;
        }
        else {
          $values[$row['delta']] = $current_value;
        }
      }
    }
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    if (!empty($this->configuration['ids'])) {
      $ids = $this->configuration['ids'];
    }
    else {
      $ids['tid']['type'] = 'integer';
      if (!empty($this->configuration['translations'])) {
        $ids['language']['type'] = 'string';
      }
    }

    if (!empty($ids['language']) && empty($ids['language']['alias'])) {
      $ids['language']['alias'] = 'td';
    }
    return $ids;
  }

}
