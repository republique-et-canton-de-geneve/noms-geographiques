<?php

namespace Drupal\ngeo_form\Form;

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
    return 'ngeo_form_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ngeo_form.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ngeo_form.settings');

    $contactContent = $config->get('contact_content');
    $form['contact_content'] = [
      '#type'           => 'text_format',
      '#title'          => $this->t('Texte de la page "Contact"'),
      '#format'         => $contactContent['format'] ?: 'basic_html',
      '#default_value'  => $contactContent['value'] ?: '',
    ];

    $anecdoteContent = $config->get('anecdote_content');
    $form['anecdote_content'] = [
      '#type'           => 'text_format',
      '#title'          => $this->t('Texte de la page "Soumettre une anecdote"'),
      '#format'         => $anecdoteContent['format'] ?: 'basic_html',
      '#default_value'  => $anecdoteContent['value'] ?: '',
    ];

    $distributionList = $config->get('distribution_list_notification');
    $form['distribution_list_notification'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Liste de distribution des notifications'),
      '#description' => $this->t('Entrez la liste des mails séparés par un point virgule'),
      '#default_value' => $distributionList ?: '',
    ];

    $form['safe_words'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Mots à filtrer dans les commentaires'),
      '#default_value' => $config->get('safe_words'),
      '#description' => $this->t('Entrez la liste des mots séparés par une virgule.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $is_valid = TRUE;
    $emails = explode(';', $form_state->getValue('distribution_list_notification'));
    foreach ($emails as $emailWithSpaces) {
      $email = trim($emailWithSpaces);
      if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        $is_valid = FALSE;
      }
    }
    if (!$is_valid) {
      $form_state->setErrorByName('distribution_list_notification',
        $this->t("La liste d'adresses e-mail n'est pas valide."));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ngeo_form.settings')
      ->set('distribution_list_notification', $form_state->getValue('distribution_list_notification'))
      ->set('safe_words', $form_state->getValue('safe_words'))
      ->set('contact_content', $form_state->getValue('contact_content'))
      ->set('anecdote_content', $form_state->getValue('anecdote_content'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
