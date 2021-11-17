<?php

namespace Drupal\chm_common\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;

/**
 * Content types spacer block.
 *
 * @Block(
 *   id = "spacer_block",
 *   admin_label = @Translation("Spacer block")
 * )
 */
class SpacerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $content = [];
    $spacer_height = $this->configuration['spacer_height'];
    $attributes = new Attribute([
      'class' => [
        'spacer-block',
      ],
      'style' => 'height:' . $spacer_height . 'px;',
      'title' => $this->t('Spacer')
    ]);

    $content['output'] = [
      '#theme' => 'spacer_block',
      '#attributes' => $attributes,
    ];
    return $content;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $form['spacer_height'] = [
      '#type' => 'number',
      '#title' => $this->t('Spacer height'),
      '#default_value' => isset($config['spacer_height']) ? $config['spacer_height'] : 0,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['spacer_height'] = $values['spacer_height'];
  }

}
