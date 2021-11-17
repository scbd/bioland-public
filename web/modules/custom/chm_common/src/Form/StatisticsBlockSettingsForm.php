<?php

namespace Drupal\chm_common\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Builds the statistics block settings form.
 */
class StatisticsBlockSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'statistics_block_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $content_types = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();
    $idx = 0;
    $rows = [];
    $headers = [
      'content_type' => $this->t('Content type'),
      'show' => $this->t('Show'),
      'singular' => $this->t('Label (singular)'),
      'plural' => $this->t('Label (plural)'),
      'url' => $this->t('URL (route)'),
      'icon' => $this->t('Icon markup classes'),
      'weight' => $this->t('Weight'),
    ];
    $idx = 0;
    $delta = count($content_types);
    foreach ($content_types as $content_type) {
      /** @var \Drupal\node\Entity\NodeType $content_type */
      $settings = \Drupal::state()->get('content_statistics_block_settings')[$content_type->id()];
      $rows[$content_type->id()]['class'] = ['draggable'];
      $name = "content_statistics_block_settings[{$content_type->id()}]";
      $rows[$content_type->id()]['data']['content_type'] = [
        'data' => [
          '#type' => 'label',
          '#title' => $content_type->label(),
          '#title_display' => 'above',
        ],
      ];
      $rows[$content_type->id()]['data']['show'] = [
        'data' => [
          '#type' => 'checkbox',
          '#name' => $name . '[show]',
          '#checked' => !empty($settings['show']),
        ],
      ];
      $rows[$content_type->id()]['data']['singular'] = [
        'data' => [
          '#type' => 'textfield',
          '#size' => 20,
          '#name' => $name . '[singular]',
          '#value' => !empty($settings['singular']) ? $settings['singular'] : $content_type->label(),
        ],
      ];
      $rows[$content_type->id()]['data']['plural'] = [
        'data' => [
          '#type' => 'textfield',
          '#size' => 20,
          '#name' => $name . '[plural]',
          '#value' => !empty($settings['plural']) ? $settings['plural'] : "",
        ],
      ];
      $rows[$content_type->id()]['data']['url'] = [
        'data' => [
          '#type' => 'textfield',
          '#size' => 20,
          '#name' => $name . '[url]',
          '#value' => !empty($settings['url']) ? $settings['url'] : "",
        ],
      ];
      $rows[$content_type->id()]['data']['icon'] = [
        'data' => [
          '#type' => 'textfield',
          '#size' => 50,
          '#name' => $name . '[icon]',
          '#value' => !empty($settings['icon']) ? $settings['icon'] : "",
        ],
      ];

      $rows[$content_type->id()]['data']['weight'] = [
        'data' => [
          '#type' => 'number',
          '#value' => isset($settings['weight']) ? $settings['weight'] : $idx,
          '#name' => $name . '[weight]',
          '#delta' => $delta,
        ],
      ];
      $idx++;

    }

    usort($rows, function ($a, $b) {
      return $a['data']['weight'] > $b['data']['weight'];
    });

    $form['#tree'] = TRUE;
    $form['content_statistics_block_settings'] = [
      '#tree' => TRUE,
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => $headers,
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save All Changes'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $submission = $form_state->getValue('content_statistics_block_settings');
    uasort($submission, function ($a, $b) {
      return $a['weight'] > $b['weight'];
    });
    \Drupal::state()->set('content_statistics_block_settings', $submission);
  }

}
