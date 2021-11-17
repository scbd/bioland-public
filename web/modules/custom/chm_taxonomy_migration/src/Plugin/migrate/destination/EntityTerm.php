<?php

namespace Drupal\chm_taxonomy_migration\Plugin\migrate\destination;

use Drupal\Core\TypedData\TranslatableInterface;
use Drupal\migrate\Plugin\migrate\destination\EntityContentBase;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Overwrites the default rollback method.
 *
 * @MigrateDestination(
 *   id = "entity:taxonomy_term"
 * )
 */
class EntityTerm extends EntityContentBase {

  /**
   * {@inheritdoc}
   */
  public function rollback(array $destination_identifier) {
    if ($this->isTranslationDestination()) {
      // Attempt to remove the translation.
      $entity = $this->storage->load(reset($destination_identifier));
      if ($entity && $entity instanceof TranslatableInterface) {
        if ($key = $this->getKey('langcode')) {
          if (isset($destination_identifier[$key])) {
            $langcode = $destination_identifier[$key];
            if ($entity->hasTranslation($langcode)) {
              // Make sure we don't remove the default translation.
              $translation = $entity->getTranslation($langcode);
              if (!$translation->isDefaultTranslation()) {
                $entity->removeTranslation($langcode);
                $entity->save();
              }
              else {
                $entity->delete();
              }
            }
          }
        }
      }
    }
    else {
      parent::rollback($destination_identifier);
    }
  }

  /**
   * Saves the entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The content entity.
   * @param array $old_destination_id_values
   *   (optional) An array of destination ID values. Defaults to an empty array.
   *
   * @return array
   *   An array containing the entity ID.
   */
  protected function save(ContentEntityInterface $entity, array $old_destination_id_values = []) {
    $parents = $entity->parent->getValue();
    foreach ($parents as $key => $parent) {
      if (!empty($parent['target_id']) && !is_numeric($parent['target_id'])) {
        unset($parents[$key]);
      }
    }
    $entity->set('parent', $parents);
    $entity->save();
    return [$entity->id()];
  }

}
