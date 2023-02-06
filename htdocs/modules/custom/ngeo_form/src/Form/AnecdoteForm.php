<?php

namespace Drupal\ngeo_form\Form;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ngeo_form\Service\NgeoFormHelper;

/**
 * Form to handle article autocomplete.
 */
class AnecdoteForm extends FormBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The email validator.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * The form helper.
   *
   * @var \Drupal\ngeo_form\Service\NgeoFormHelper
   */
  protected $ngeoFormHelper;

  /**
   * Constructs a FeedbackForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    LoggerInterface $logger,
    MailManagerInterface $mail_manager,
    EmailValidatorInterface $email_validator,
    NgeoFormHelper $ngeo_form_helper) {

    $this->configFactory = $config_factory->get('ngeo_form.settings');
    $this->logger = $logger;
    $this->mailManager = $mail_manager;
    $this->emailValidator = $email_validator;
    $this->ngeoFormHelper = $ngeo_form_helper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('logger.factory')->get('action'),
      $container->get('plugin.manager.mail'),
      $container->get('email.validator'),
      $container->get('ngeo_form.form_helper')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'anecdote_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $wrapperClass = [
      'class' => ['mb-3'],
    ];

    $title = '';
    if ($this->getRequest()->query->has('title')) {
      $title = $this->getRequest()->query->get('title');
    }

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Lieu concerné'),
      '#required' => TRUE,
      '#default_value' => html_entity_decode($title, ENT_QUOTES | ENT_XML1, 'UTF-8'),
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#wrapper_attributes' => $wrapperClass,
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Anecdote'),
      '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#wrapper_attributes' => $wrapperClass,
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nom'),
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#wrapper_attributes' => $wrapperClass,
    ];

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prénom'),
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#wrapper_attributes' => $wrapperClass,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Adresse e-mail'),
      '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#wrapper_attributes' => $wrapperClass,
    ];

    $form['send'] = [
      '#type' => 'submit',
      '#name' => 'submit-message',
      '#value' => $this->t("Envoyer"),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->hasValue('title')) {
      $form_state->setErrorByName('title', $this->t("Le titre est requis."));
    }

    if (!$form_state->hasValue('email')) {
      $form_state->setErrorByName('email', $this->t("L'adresse e-mail est requise."));
    }

    if (!$form_state->hasValue('message')) {
      $form_state->setErrorByName('message', $this->t("Le texte est requis."));
    }

    if (!$this->emailValidator->isValid($form_state->getValue('email'))) {
      $form_state->setErrorByName('email', $this->t("L'adresse e-mail est invalide."));
    }

    if (mb_strlen($form_state->getValue('message')) < 10) {
      $form_state->setErrorByName('message', $this->t("Le texte est trop court."));
      return;
    }

    if ($this->ngeoFormHelper->isSpam($form_state->hasValue('message'))) {
      $this->logger("ngeo_form")->info("Anecdote - SPAM detected");
      // On ne lève pas d'erreur.
      return;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $data = [];
    $fields = [
      'title',
      'last_name',
      'first_name',
      'email',
      'message',
    ];

    foreach ($fields as $field_name) {
      if ($form_state->hasValue($field_name)) {
        $data[$field_name] = Xss::filter($form_state->getValue($field_name));
        $data[$field_name] = strip_tags($data[$field_name]);
      }
    }

    // Send email.
    $this->sendNotificationMail($data);

    // Confirmation message.
    $this->messenger()->addStatus($this->t('Votre anecdote a bien été envoyée, merci pour votre contribution.'));
  }

  /**
   * Envoie l'email de notification.
   *
   * @param array|null $data
   */
  private function sendNotificationMail($data) {
    $to = $this->ngeoFormHelper->buildListEmailsFromListNotification($this->configFactory->get('distribution_list_notification'));
    $params = [
      'message' => '',
      'subject' => t('Noms géographiques - Anecdote'),
    ];

    $params['message'] .= "Lieu : " . $data['title'] . "\n";

    $params['message'] .= "Anecdote : " . $data['message'] . "\n";
    $params['message'] .= "\n";

    if (!empty($data['last_name'])) {
      $params['message'] .= "Nom : " . $data['last_name'] . "\n";
    }

    if (!empty($data['first_name'])) {
      $params['message'] .= "Prénom : " . $data['first_name'] . "\n";
    }

    $params['message'] .= "Adresse e-mail : " . $data['email'] . "\n";
    $params['message'] .= "\n";

    $params['message'] .= "Soumise à : " . (new \DateTime())->format('Y-m-d H:i:s') . "\n";
    $langcode = \Drupal::currentUser()->getPreferredLangcode();

    $this->mailManager->mail('ngeo_form', 'sendAnecdoteMail', $to, $langcode, $params, NULL, TRUE);
  }

}
