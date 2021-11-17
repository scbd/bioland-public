<?php

namespace Drupal\chm_common\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class CHMCommonRouteSubscriber plugin subscribes to intercept routes.
 *
 * @package Drupal\chm_common\Routing
 */
class CHMCommonRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('system.site_information_settings')) {
      // Remove old requirement 'administer site configuration' because it was
      // too powerful to be given to site admins.
      $route->setRequirement('_permission', 'bioland edit basic site settings');
      // Next, we need to set the value for _form to the form we have created.
      $route->setDefault('_form', 'Drupal\chm_common\Form\CHMBasicSiteForm');
    }

    if ($route = $collection->get('system.cron_settings')) {
      $route->setRequirement('_permission', 'bioland manage cron settings');
    }

    if ($route = $collection->get('system.performance_settings')) {
      $route->setRequirement('_permission', 'bioland access cache rebuild');
    }

    if ($route = $collection->get('system.theme_settings_theme')) {
      $route->setRequirement('_permission', 'bioland edit theme settings');
      $route->setRequirement('_custom_access', 'Drupal\chm_common\Plugin\Access\CHMAccess::accessTheme');
    }

    $datetime_routes = ['entity.date_format.collection', 'system.date_format_add'];
    foreach ($datetime_routes as $route_name) {
      if ($route = $collection->get($route_name)) {
        $route->setRequirement('_permission', 'bioland manage datetime settings');
      }
    }

    if ($route = $collection->get('system.regional_settings')) {
      $route->setRequirement('_permission', 'bioland manage regional settings');
    }

    if ($route = $collection->get('system.site_maintenance_mode')) {
      $route->setRequirement('_permission', 'bioland manage maintenance mode');
    }

  }

}
