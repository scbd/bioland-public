<?php

namespace Drupal\chm_www\Plugin\Block;

/**
 * @file
 * SiteSwitcherBlock contains the block used to switch CHM sites.
 */

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\TermInterface;

/**
 * Block to switch between CHM sites.
 *
 * @Block(
 *   id = "chm_site_switcher_block",
 *   admin_label = @Translation("Switch to another CHM site"),
 * )
 */
class CHMSiteSwitcherBlock extends BlockBase {

  const CONFIG_EMPTY_OPTION = 'empty_option';
  const CONFIG_EMPTY_OPTION_DEFAULT = 'Switch to another CHM site';

  /**
   * Block configuration form.
   *
   * @inheritdoc
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $empty_option = isset($config[self::CONFIG_EMPTY_OPTION])
      ? $this->t($config[self::CONFIG_EMPTY_OPTION])
      : $this->t(self::CONFIG_EMPTY_OPTION_DEFAULT);

    $form['empty_option'] = [
      "#type" => 'textfield',
      '#title' => $this->t('Empty option label'),
      '#description' => $this->t('The label which appears as first option in site selecto.'),
      '#default_value' => $empty_option,
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * Block configuration submit method.
   *
   * @inheritdoc
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration[self::CONFIG_EMPTY_OPTION] = $values[self::CONFIG_EMPTY_OPTION];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $content['#cache'] = ['max-age' => 0];
    $rows = $this->getChmSites();
    $options = [];
    /** @var \stdClass $row */
    foreach ($rows as $row) {
      if (!$row->field_url->count()) {
        continue;
      }
      if ($row->field_countries->count()) {
        $url = $row->field_url[0]->uri;
        $country = $row->field_countries->get(0)->entity;
        if ($country instanceof TermInterface) {
          $options[$url] = $country->getName();
        }
      }
    }
    if (!empty($options)) {
      $config = $this->getConfiguration();
      $empty_option = !empty($config[self::CONFIG_EMPTY_OPTION])
        ? $config[self::CONFIG_EMPTY_OPTION]
        : self::CONFIG_EMPTY_OPTION_DEFAULT;
      $options = ['' => $empty_option] + $options;
      $content['output'] = [
        '#type' => 'select',
        '#options' => $options,
        '#attributes' => [
          'onChange' => 'if (this.value) window.location.href=this.value;',
        ],
      ];
    }
    return $content;
  }

  /**
   * Query and get the list of CHM sites.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]|static[]
   *   Return a list of CHM site nodes.
   */
  private function getChmSites() {

    $q = \Drupal::entityQuery('node');
    $q->condition('type', 'chm_site');

    return \Drupal::entityTypeManager()->getStorage('node')->loadMultiple(array_values($q->execute()));
  }

}
