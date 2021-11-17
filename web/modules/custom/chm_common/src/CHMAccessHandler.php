<?php

namespace Drupal\chm_common;

use Drupal\Core\Access\AccessResult;
use Drupal\user\UserAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the custom access control handler for date_format entities.
 */
class CHMAccessHandler extends UserAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($operation === 'view label') {
      return AccessResult::allowed();
    }
    // Locked date formats cannot be updated or deleted.
    elseif (in_array($operation, ['update', 'delete'])) {
      if ($entity->isLocked()) {
        return AccessResult::forbidden('The DateFormat config entity is locked.')->addCacheableDependency($entity);
      }
      else {
        return AccessResult::allowed();
      }
    }

    return AccessResult::allowed();
  }

}
