<?php

namespace Drupal\chm_common;

/**
 * Class SecurityHelper is handling custom bioland security and accounts.
 *
 * @package Drupal\chm_common
 */
class SecurityHelper {

  const ROLE_CONTENT_MANAGER = 'content_manager';
  const ROLE_SITE_MANAGER = 'site_manager';
  const ROLE_SYSTEM_ADMINISTRATOR = 'administrator';

  const PERMISSION_CREATE_NATIONAL_ACCOUNT = 'bioland create national accounts';

  /**
   * Show/hide roles input in user profile form depending on site permissions.
   *
   * @param array $form
   *   The editing form.
   */
  public static function userFieldRolesAccess(array &$form) {
    $user = \Drupal::currentUser();
    $input_roles =& $form['account']['roles'];
    $input_roles['#access'] = $input_roles['#access'] || $user->hasPermission(self::PERMISSION_CREATE_NATIONAL_ACCOUNT);
    // Other roles than 'System administrators' can only create lesser accounts.
    if (!self::userIsAdministrator()) {
      drupal_set_message(
        t('Choose <b>@cm_role_name</b> role to allow other people to contribute content into your website. <b>@sm_role_name</b> allows full management of the website!',
          ['@cm_role_name' => 'Content Manager', '@sm_role_name' => 'Site Manager']),
        'warning');
      $input_roles['#options'] = [
        'site_manager' => $input_roles['#options']['site_manager'],
        'content_manager' => $input_roles['#options']['content_manager'],
        'contributor' => $input_roles['#options']['contributor'],
        'authenticated' => $input_roles['#options']['authenticated'],
      ];
      //$input_roles['#default_value'] = ['content_manager'];

      // Simplify and enhance account form
      // Hide 'path alias' field to simply the form.
      $form['path']['#access'] = FALSE;
      $form['account']['status']['#description'] = t('<b>Blocked</b> accounts cannot log in into the website, choose only to disable access.');
      $form['account']['notify']['#description'] = t('Send user an welcome email with log in information.');
    }
  }

  /**
   * Current user has administrative privileges on this website.
   *
   * @return bool
   *   TRUE if current user can manage modules.
   */
  public static function userIsAdministrator() {
    $user = \Drupal::currentUser();
    return $user->hasPermission('administer modules');
  }

  /**
   * Add a note to the page to avoid issues later in the project.
   */
  public static function showRestrictedFunctionalityWarning() {
    drupal_set_message(t('This screen has restricted functionality. All the options are shown to the technical support team.'), 'status');
  }

}
