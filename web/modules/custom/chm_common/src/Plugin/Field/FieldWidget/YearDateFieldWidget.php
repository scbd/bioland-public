<?php

namespace Drupal\chm_common\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'year_date_field_widget' widget.
 *
 * @FieldWidget(
 *   id = "year_date_field_widget",
 *   label = @Translation("Year"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class YearDateFieldWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = [];
    $years = [];
    for ($i = date("Y"); $i > 0; $i--) {
      $years["$i-01-01"] = $i;
    }
    $element['value'] = $element + [
      '#type' => 'select',
      '#options' => $years,
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
    ];
    $element['value']['#title'] = $this->fieldDefinition->getLabel();
    $element['value']['#description'] = $this->fieldDefinition->getDescription();

    return $element;
  }

}
