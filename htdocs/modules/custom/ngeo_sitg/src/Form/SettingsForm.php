<?php

namespace Drupal\ngeo_sitg\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Contact forms settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ngeo_sitg_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ngeo_sitg.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ngeo_sitg.settings');

    $form['sitg_map_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL de la carte'),
      '#default_value' => $config->get('sitg_map_url'),
      '#required' => TRUE,
      '#size' => 250,
      '#maxlength' => 250,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ngeo_sitg.settings')
      ->set('sitg_map_url', $form_state->getValue('sitg_map_url'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
