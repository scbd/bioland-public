<?php

namespace Drupal\chm_common\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Content types statistics block.
 *
 * @Block(
 *   id = "statistics_block",
 *   admin_label = @Translation("Content types statistics block")
 * )
 */
class StatisticsBlock extends BlockBase {

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
    // @codingStandardsIgnoreStart
    $settings = \Drupal::state()->get('content_statistics_block_settings');
    $content = [];
    $data = [];
    $show_statistics = !empty($this->configuration['show_statistics']);
    $current_uri = \Drupal::request()->getRequestUri();
    foreach ($settings as $ctype => $setting) {
      if (!empty($setting['show']) && $setting['show']) {
        $nids = \Drupal::entityQuery('node')->condition('type', $ctype)->condition('status', 1)->execute();
        $count = count($nids); //status=1 is published only
        if ($show_statistics) {
          $label = $count == 1 ? $count . ' ' . $this->t($setting['singular']) : $count . ' ' . $this->t($setting['plural']);
        }
        else {
          $label = $this->t($setting['plural']);
        }
	$target_uri = Url::fromUri('internal:'.$setting['url'])->toString();
        $data[$ctype] = [
          '#label' => $label,
          '#icon' => $setting['icon'],
          '#url' => $target_uri == $current_uri ? NULL : $target_uri ,
        ];
      }
    }
    // @codingStandardsIgnoreEnd
    $content['output'] = [
      '#theme' => 'content_types_block',
      '#data' => $data,
      '#class' => !empty($show_statistics) ? 'statistics-block' : 'information-block',
    ];
    return $content;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $form['show_statistics'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show statistics'),
      '#default_value' => isset($config['show_statistics']) ? $config['show_statistics'] : 0,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['show_statistics'] = $values['show_statistics'];
  }

}
