<?php

use Drupal\node\Entity\Node;

// Source: https://eiriksm.dev/find-blocks-used-with-layout-builder
// use with drush @alias scr 

// Get all the entity ids of all the nodes referencing a field called
// layout_builder__layout. If you want the blocks and/or layouts of another
// entity type, the database table and entity loading should be switched out
// with something corresponding.
$rows = \Drupal::database()
  ->select('node__layout_builder__layout', 'ns')
  ->fields('ns', ['entity_id'])
  ->groupBy('ns.entity_id')
  ->execute();
// This will hold all of our block plugin ids.
$block_plugin_ids = [];
// And this will keep track of which layout ids we have.
$layout_ids = [];
foreach ($rows as $row) {
  /** @var \Drupal\node\Entity\Node $node */
  $node = Node::load($row->entity_id);
  if (!$node) {
    // If we can not load the node, something must be wrong.
    continue;
  }
  if (!$node->hasField('layout_builder__layout') || $node->get('layout_builder__layout')->isEmpty()) {
    // If the field is empty or does not exist, something must be wrong.
    continue;
  }
  // This will get all of the sections of the node.
  $layout = $node->get('layout_builder__layout')->getValue();
  foreach ($layout as $item) {
    /** @var \Drupal\layout_builder\Section $section */
    $section = $item['section'];
    // This will overwrite the array key of the layout ID if it exists. But that
    // is ok.
    $layout_ids[$section->getLayoutId()] = TRUE;
    // You can also operate directly on the section object, but getting the
    // array structure makes it more convenient for this case.
    $section_array = $section->toArray();
    foreach ($section_array["components"] as $component) {
      // This ID will correspond to the ID we have in the plugin. For example,
      // the page title block has an ID of "page_title_block".
      $id = $component["configuration"]["id"];
      // We only want unique plugin IDs.
      if (in_array($id, $block_plugin_ids)) {
        continue;
      }
      $block_plugin_ids[] = $id;
    }
  }
}
// This will now be an array of plugin ids like so:
// ['page_title_block', 'my_other_plugin'].
print_r($block_plugin_ids);
// This will now be an array with layout ids as keys. Like so:
// ['layout_onecol' => TRUE, 'layout_twocol' => TRUE].
print_r(array_keys($layout_ids));

