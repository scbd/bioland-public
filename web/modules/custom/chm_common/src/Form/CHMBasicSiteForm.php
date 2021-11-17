<?php

namespace Drupal\chm_common\Form;

use Drupal\chm_common\WebsiteUtilities;
use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\SiteInformationForm;

/**
 * Class CHMBasicSiteForm extends system.site information form.
 *
 * @package Drupal\chm_common\Form
 */
class CHMBasicSiteForm extends SiteInformationForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /*
      $site_config = $this->config('system.site');
      This approach no longer includes overrides from settings.php in the latest Drupal
    */

    $site_config = \Drupal::config('system.site');

    $form = parent::buildForm($form, $form_state);
    $options = ['' => $this->t('-- Not a national portal --')]
      + WebsiteUtilities::getCountryListKeyedByIso3();
    $form['site_information']['chm_government'] = [
      '#type' => 'select',
      '#title' => t('Government'),
      '#options' => $options,
      '#default_value' => $site_config->get('chm_government'),
      '#description' => $this->t("For national websites, this is the government is linked to. <br><b>Note: This can only be changed through technical support, as it affects data imported from remote services.</b>"),
      '#attributes' => ['disabled' => 'disabled'],
    ];
    return $form;
  }

}
