<?php

namespace Drupal\chm_taxonomy_migration\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "chm_json",
 *   title = @Translation("CHM JSON")
 * )
 */
class CHMJson extends Json {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    if (!empty($configuration['migration_id'])) {
      // Allow the source url to be overridden in settings.php.
      $urls = \Drupal::config("migrate_plus.migration.{$configuration['migration_id']}")->get('source')['urls'];
      $this->urls = is_array($urls) ? $urls : [$urls];
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function fetchNextRow() {
    $current = $this->iterator->current();
    if ($current) {
      foreach ($this->fieldSelectors() as $field_name => $selector) {
        $selector = explode('/', $selector);
        $value = $current;
        foreach ($selector as $subselector) {
          if (is_array($value) && array_key_exists($subselector, $value)) {
            $value = $value[$subselector];
          }
        }
        $this->currentItem[$field_name] = $value;
      }
      $this->iterator->next();
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function getSourceData($url) {
    $iterator = $this->getSourceIterator($url);
    $items = [];
    $page = 0;

    while (!empty($iterator->current())) {
      // Recurse through the result array. When there is an array of items at
      // the expected depth, pull that array out as a distinct item.
      $identifierDepth = $this->itemSelector;
      $iterator->rewind();
      while ($iterator->valid()) {
        $item = $iterator->current();
        if (is_array($item) && $iterator->getDepth() == $identifierDepth) {
          $items[] = $item;
        }
        $iterator->next();
      }

      // Get next page.
      $iterator = $this->getSourceIterator($url . '?page=' . ++$page);
    }

    return $items;
  }

}
