<?php

// Go through all the entities and find translation of a particular language
// Then remove it (and - accidentally - ALL other non-registered languages, it seems)
// Fixes the issue described in https://www.drupal.org/project/drupal/issues/2540876 (error after removing a language). 
// The solution is based on the comment #18 in above
// Adjustment is based on examples in http://hojtsy.hu/blog/2015-nov-11/drupal-8-multilingual-tidbits-19-content-translation-development

$lang = 'zh-hans';

$transTypes = array();
$definitions = \Drupal::entityTypeManager()->getDefinitions();
foreach ( $definitions as $id => $definition ) {
  if ( $definition->isTranslatable() ) {
    $transTypes[] = $id;
  }
}

foreach ($transTypes as $type ) {
  $entities = get_entities($type);
  print "Processing " . count($entities) ." entities of type $type\n";

  foreach ($entities as $id) {
    $entity = \Drupal::entityTypeManager()->getStorage($type)->load($id);
    if ($entity->hasTranslation($lang) ) {
       print "  Found $lang translation in type $type, id $id. Removing!\n";
       $entity->removeTranslation($lang); 
       $entity->save();
    }
  }
}

function get_entities( $entity_type ) {
  $query = \Drupal::entityQuery($entity_type);
  $results = $query->execute();
  return $results;
}
