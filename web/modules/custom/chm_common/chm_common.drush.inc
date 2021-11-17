<?php

/**
 * @file
 * Command-line tools to aid performing Bioland actions.
 */

use Drupal\Core\Serialization\Yaml;
use Drush\Log\LogLevel;
use Drupal\user\UserInterface;
use Drupal\user\Entity\User;

/**
 * Implements hook_drush_command().
 */
function chm_common_drush_command() {
  $items['bioland-clear-translations'] = [
    'description' => 'Delete all node and taxonomy translations for extra language other than `en`',
    'options' => [
      'dry-run' => 'Do not run any deletion, just show the content to be deleted'
    ],
    'arguments' => [
      'language' => 'Language to remove'
    ],
    'examples' => [],
    'drupal dependencies' => [],
    'aliases' => ['bct'],
  ];
  $items['import-default-content'] = [
    'description' => 'Import all default content using content_sync module.',
    'arguments' => [
      'type' => 'The specific type of content to import, defaults to "all". Can be: "all", "block", "file", "menu", "node", "taxonomy", "user".',
    ],
  ];
  $items['user-update-information'] = [
    'description' => 'Update user mail, password, username, roles.',
    'aliases' => array('uuinfo'),
    'arguments' => array(
      'uid' => 'The user id',
    ),
    'required-arguments' => 1,
    'examples' => array(
      'drush user-update-information 8 --mail=billgates@microsoft.com --password=somepassword --username=someguy --roles=administrator,content_manager ' =>
        'Updates mail, password, username and roles for user 8.',
    ),
    'options' => [
      'mail' => array(
        'description' => 'An email adrress.',
        'example-value' => 'me@example.com',
      ),
      'password' => array(
        'description' => 'A password.',
        'example-value' => 'somepassword',
      ),
      'username' => array(
        'description' => 'A username for the user.',
        'example-value' => 'someguy',
      ),
      'roles' => array(
        'description' => 'A comma delimited list of roles.',
        'example-value' => 'administrator,content_manager',
      ),
    ],
  ];
  return $items;
}

function drush_chm_common_bioland_clear_translations($langcode) {
  $dry_run = drush_get_option('dry-run', FALSE);

  /** @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface $eti */
  $eti = \Drupal::service('entity_type.bundle.info');
  foreach ($eti->getAllBundleInfo() as $bundle_name => $row) {
    $et = \Drupal::entityTypeManager()->getStorage($bundle_name)->getEntityType();
    if ($et->isTranslatable()) {
      $ids = \Drupal::entityTypeManager()
        ->getStorage($bundle_name)
        ->loadMultiple();
      /**
       * @var integer $id
       * @var \Drupal\Core\Entity\ContentEntityBase $entity
       */
      foreach ($ids as $id => $entity) {
        if ($entity->hasTranslation($langcode)) {
          echo $bundle_name . ':' . $id . "\n";
          if ($dry_run) {
            continue;
          }
          $translation = $entity->getTranslation($langcode);
          if ($translation->isDefaultTranslation()) {
            $entity->delete();
          }
          else {
            $entity->removeTranslation($langcode);
            $entity->save();
          }
        }
      }
    }
  }
}

function drush_chm_common_import_default_content($type = 'all') {
  global $content_directories;
  if (!empty($content_directories['sync'])) {
    /** @var \Drupal\content_sync\Importer\ContentImporter $contentImporter */
    $contentImporter = \Drupal::service('content_sync.importer');
    $directory = $content_directories['sync'];
    $files = array_diff(scandir($directory), ['..', '.']);
    usort($files, function ($a, $b) {
      $aw = _import_default_content_get_file_weight($a);
      $bw = _import_default_content_get_file_weight($b);
      if ($aw == $bw) {
        return ($a < $b) ? -1 : 1;
      }
      return ($aw < $bw) ? -1 : 1;
    });
    foreach ($files as $file) {
      if ($type == 'all' || preg_match('/^' . $type . '/', $file)) {
        $data = Yaml::decode(file_get_contents($directory . '/' . $file));
        try {
          $entity = $contentImporter->importEntity($data);
          if (!empty($entity)) {
            drush_log('Imported content: ' . $file, LogLevel::SUCCESS);
          }
          else {
            drush_log('Could not import content: ' . $file, LogLevel::ERROR);
          }
        }
        catch (\Exception $e) {
          drush_log('Could not import content: ' . $file, LogLevel::ERROR);
        }
      }
    }
  }

  // Add news and events to frontpage slideshow.
  $slideshow = \Drupal\entityqueue\Entity\EntitySubqueue::load('slideshow');
  $items = \Drupal::entityQuery('node')
    ->condition('type', ['news', 'event'], 'IN')
    ->condition('status', TRUE)
    ->execute();
  $slideshow->items->setValue($items);
  $slideshow->save();

  // Configure statistics block.
  \Drupal::state()->set('content_statistics_block_settings', array (
    'article' =>
      array (
        'singular' => 'Web page',
        'plural' => '',
        'url' => '',
        'icon' => '',
        'weight' => '0',
      ),
    'cbd_nfp' =>
      array (
        'singular' => 'National Focal Point',
        'plural' => '',
        'url' => '',
        'icon' => '',
        'weight' => '1',
      ),
    'ecosystem' =>
      array (
        'show' => '1',
        'singular' => 'Ecosystem',
        'plural' => 'Ecosystems',
        'url' => '/ecosystems',
        'icon' => 'recycle',
        'weight' => '2',
      ),
    'document' =>
      array (
        'show' => '1',
        'singular' => 'Document',
        'plural' => 'Documents',
        'url' => '/documents',
        'icon' => 'folder-open-o',
        'weight' => '3',
      ),
    'event' =>
      array (
        'show' => '1',
        'singular' => 'Event',
        'plural' => 'Events',
        'url' => '/events',
        'icon' => 'calendar',
        'weight' => '4',
      ),
    'faq' =>
      array (
        'singular' => 'FAQ',
        'plural' => '',
        'url' => '',
        'icon' => 'question-circle-o',
        'weight' => '6',
      ),
    'gallery' =>
      array (
        'show' => '1',
        'singular' => 'Photo album',
        'plural' => 'Photo albums',
        'url' => '/photo-galleries',
        'icon' => 'picture-o',
        'weight' => '7',
      ),
    'landing_page' =>
      array (
        'singular' => 'Landing page',
        'plural' => '',
        'url' => '',
        'icon' => '',
        'weight' => '8',
      ),
    'national_target' =>
      array (
        'show' => '1',
        'singular' => 'National target',
        'plural' => 'National targets',
        'url' => '/implementation/targets',
        'icon' => 'dot-circle-o',
        'weight' => '9',
      ),
    'link' =>
      array (
        'show' => '1',
        'singular' => 'Related website',
        'plural' => 'Related websites',
        'url' => '/related-websites',
        'icon' => 'link',
        'weight' => '9',
      ),
    'news' =>
      array (
        'show' => '1',
        'singular' => 'News',
        'plural' => 'News',
        'url' => '/news',
        'icon' => 'newspaper-o',
        'weight' => '10',
      ),
    'organization' =>
      array (
        'show' => '1',
        'singular' => 'Organization',
        'plural' => 'Organizations',
        'url' => '/organizations',
        'icon' => 'building-o',
        'weight' => '11',
      ),
    'page' =>
      array (
        'singular' => 'Basic page',
        'plural' => '',
        'url' => '',
        'icon' => '',
        'weight' => '12',
      ),
    'person' =>
      array (
        'singular' => 'Person',
        'plural' => '',
        'url' => '',
        'icon' => '',
        'weight' => '13',
      ),
    'project' =>
      array (
        'show' => '1',
        'singular' => 'Project',
        'plural' => 'Projects',
        'url' => '/projects',
        'icon' => 'sitemap',
        'weight' => '14',
      ),
    'protected_area' =>
      array (
        'show' => '1',
        'singular' => 'Protected area',
        'plural' => 'Protected areas',
        'url' => '/protected-areas',
        'icon' => 'globe',
        'weight' => '15',
      ),
    'video' =>
      array (
        'show' => '1',
        'singular' => 'Video',
        'plural' => 'Videos',
        'url' => '/videos',
        'icon' => 'video-camera',
        'weight' => '17',
      ),
    'webform' =>
      array (
        'singular' => 'Webform',
        'plural' => '',
        'url' => '',
        'icon' => '',
        'weight' => '18',
      ),
  ));
}

function _import_default_content_get_file_weight($name) {
  switch (TRUE) {
    case preg_match('/^user/', $name):
      return 1;

    case preg_match('/^file/', $name):
      return 2;

    case preg_match('/^block/', $name):
      return 3;

    case preg_match('/^node/', $name):
      return 4;

    case preg_match('/^paragraph/', $name):
      return 5;

    case preg_match('/^menu/', $name):
      return 6;

    default:
      return 100;
  }
}

function drush_chm_common_user_update_information($uid) {
  $user = User::load($uid);
  if ($user instanceof UserInterface) {
    $mail = drush_get_option('mail');
    if (!empty($mail)) {
      $user->setEmail($mail);
    }

    $password = drush_get_option('password');
    if (!empty($password)) {
      $user->setPassword($password);
    }

    $username = drush_get_option('username');
    if (!empty($username)) {
      $user->setUsername($username);
    }

    $roles = explode(',', drush_get_option('roles', ''));
    if (!empty($roles)) {
      foreach ($user->getRoles() as $role) {
        $user->removeRole($role);
      }
      foreach ($roles as $role) {
        $user->addRole($role);
      }
    }

    $user->save();
  }
}
