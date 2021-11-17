<?php

namespace Drupal\chm_common\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class DashboardController extends ControllerBase {

  /**
   * Returns a simple page to render additional content inside.
   *
   * @return array
   *   A simple render array.
   */
  public function page() {
    $element = [
      '#markup' => '',
    ];
    return $element;
  }

}
