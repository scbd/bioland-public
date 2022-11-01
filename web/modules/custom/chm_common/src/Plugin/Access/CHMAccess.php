<?php

namespace Drupal\chm_common\Plugin\Access;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Class used to set custom access on CHM functionalities.
 */
class CHMAccess {

  /**
   * Only give access to biotheme for any user besides user1.
   */
  public function accessTheme(AccountInterface $account, $theme) {
    return AccessResult::allowedIf(in_array('authenticated', $account->getRoles()) && in_array('administrator', $account->getRoles()));
  }

}
