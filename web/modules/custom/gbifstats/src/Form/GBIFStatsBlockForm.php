<?php

namespace Drupal\gbifstats\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * GBIF Stats block form
 */
class GBIFStatsBlockForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'gbifstats_block_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        // Defining the country code
        $form['country_code'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Country Code'),
            '#default_value' => 'FR',
            '#description' => $this->t('Write the two letters of the country code'),
        ];

        // Defining all of the GBIF node informations
        $form['node_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Node name'),
            '#default_value' => 'GBIF France',
            '#description' => $this->t('The name of the national node'),
        ];

        $form['website'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Website'),
            '#default_value' => 'http://www.gbif.fr',
            '#description' => $this->t('The URL of the website'),
        ];

        $form['head_delegation'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Head of the delegation'),
            '#default_value' => 'Eric Chenin',
            '#description' => $this->t('The name of the head of the national delegation'),
        ];

        $form['node_manager'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Node Manager'),
            '#default_value' => 'Anne-Sophie Archambeau',
            '#description' => $this->t('The name of the node mananager'),
        ];

        $form['link_page_GBIF'] = [
            '#type' => 'textfield',
            '#title' => $this->t('GBIF page of the node'),
            '#default_value' => 'https://www.gbif.org/country/FR/summary',
            '#description' => $this->t('The URL adresse to the GBIF page of the node'),
        ];

        // Submit.
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Generate'),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $country_code = $form_state->getValue('country_code');
        $node_name = $form_state->getValue('node_name');
        $website = $form_state->getValue('website');
        $head_delegation = $form_state->getValue('head_delegation');
        $node_manager = $form_state->getValue('node_manager');
        $link_page_GBIF = $form_state->getValue('link_page_GBIF');

        if (!is_string($country_code) || !is_string($head_delegation) || !is_string($node_manager) || !is_string($node_name)) {
            $form_state->setErrorByName('country_code', $this->t('Please use only letters.'));
        }

        if (strlen($country_code ) < 2 || strlen($country_code ) > 2) {
            $form_state->setErrorByName('country_code', $this->t('Country code are two letters only'));
        }

        //TODO : ajout test adresse web pour $website et $link_page_GBIF
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $form_state->setRedirect('gbifstats.generate', [
            'country_code' => $form_state->getValue('country_code'),
            'node_name' => $form_state->getValue('node_name'),
            'website' => $form_state->getValue('website'),
            'head_delegation' => $form_state->getValue('head_delegation'),
            'node_manager' => $form_state->getValue('node_manager'),
            'link_page_GBIF' => $form_state->getValue('link_page_GBIF'),
        ]);
    }

}