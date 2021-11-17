<?php

namespace Drupal\gbifstats\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a GBIF Stats block with which you can generate informations.
 *
 * @Block(
 *   id = "gbifstats_block",
 *   admin_label = @Translation("GBIF Stats block"),
 * )
 */
class GBIFStatsBlock extends BlockBase implements BlockPluginInterface {

    /**
     * {@inheritdoc}
     */
    public function build() {
        // Return the form @ Form/GBIFStatsBlockForm.php.
        return \Drupal::formBuilder()->getForm('Drupal\gbifstats\Form\GBIFStatsBlockForm');
    }

    /**
     * {@inheritdoc}
     */
    protected function blockAccess(AccountInterface $account) {
        return AccessResult::allowedIfHasPermission($account, 'generate GBIF Stats');
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {

        $form = parent::blockForm($form, $form_state);

        $config = $this->getConfiguration();

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $this->setConfigurationValue('gbifstats_block_settings', $form_state->getValue('gbifstats_block_settings'));
    }

}