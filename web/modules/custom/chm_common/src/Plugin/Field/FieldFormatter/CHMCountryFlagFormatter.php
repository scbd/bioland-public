<?php

namespace Drupal\chm_common\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'chm_country_flag' formatter.
 *
 * @FieldFormatter(
 *   id = "chm_country_flag",
 *   label = @Translation("CHM Country Flag"),
 *   field_types = {
 *      "link"
 *   }
 * )
 */
class CHMCountryFlagFormatter extends LinkFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'img_height' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['img_height'] = [
      '#type' => 'number',
      '#title' => t('Image height'),
      '#field_suffix' => t('px'),
      '#default_value' => $this->getSetting('img_height'),
      '#min' => 1,
      '#description' => t('Leave blank to use image original height.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $settings = $this->getSettings();

    if (!empty($settings['img_height'])) {
      $summary[] = t('Image height: @heightpx', ['@height' => $settings['img_height']]);
    }
    else {
      $summary[] = t('Original image');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    $settings = $this->getSettings();

    if (!empty($elements)) {
      foreach ($elements as &$element) {
        $img_width = '';
        $img_height = '';
        $img_url = $element['#title'];

        if (!$this->checkRemoteFile($img_url) or empty($img_url)) {
          $element = $this->renderDefaultFlag();
        }
        else {
          $img_size = getimagesize($img_url);
          if ($img_size) {
            $img_width = $img_size[0];
            $img_height = $img_size[1];
          }
          if (!empty($settings['img_height'])) {
            $img_width = $img_width * $settings['img_height'] / $img_height;
            $img_height = $settings['img_height'];
          }

          $element = [
            '#theme' => 'chm_country_flag',
            '#url' => $img_url,
            '#width' => $img_width,
            '#height' => $img_height,
          ];
        }
      }
    }
    else {
      $elements[] = $this->renderDefaultFlag();
    }
    return $elements;
  }

  /**
   * Creates default flag element.
   */
  protected function renderDefaultFlag() {
    $settings = $this->getSettings();

    $default_flag_url = '/' . drupal_get_path('module', 'chm_common') . '/images/default_flag.png';
    $default_flag_height = 275;
    $default_flag_width = 413;

    $img_url = $default_flag_url;
    $img_width = $default_flag_width;
    $img_height = $default_flag_height;

    if (!empty($settings['img_height'])) {
      $img_width = $img_width * $settings['img_height'] / $img_height;
      $img_height = $settings['img_height'];
    }

    $element = [
      '#theme' => 'chm_country_flag',
      '#url' => $img_url,
      '#width' => $img_width,
      '#height' => $img_height,
    ];

    return $element;
  }

  /**
   * Checks if remote file at url is valid.
   *
   * @param string $url
   *   The url of the file to be checked.
   */
  protected static function checkRemoteFile($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (curl_exec($ch) !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

}
