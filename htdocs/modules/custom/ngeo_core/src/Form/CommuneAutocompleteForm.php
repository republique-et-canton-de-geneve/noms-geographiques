<?php

namespace Drupal\ngeo_core\Form;

use Drupal\taxonomy\TermStorageInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to handle article autocomplete.
 */
class CommuneAutocompleteForm extends FormBase {

  /**
   * The Term storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  protected TermStorageInterface $termStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commune_autocomplete';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // The group allows to add CSS style.
    $form['all'] = [
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#attributes' => ['class' => ["group-commune"]],
    ];

    // The input field.
    $form['all']['commune'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nom de commune'),
      '#autocomplete_route_name' => 'ngeo_core.autocomplete.communes',
      '#attributes' => [
        'class' => ['form-control'],
        'placeholder' => $this->t("Rechercher une commune..."),
      ],
      '#label_attributes' => ['class' => ['visually-hidden']],
      '#ajax' => [
        'event' => 'autocompleteclose',
        'callback' => '::updateLink',
        'progress' => [
          'type' => '',
          'message' => '',
        ],
      ],
    ];

    // The link that brings to the chosen commune.
    $form['all']['go-link'] = [
      '#title' => Markup::create('<span aria-hidden="true" class="fas fa-arrow-right"></span>'),
      '#type' => 'link',
      '#url' => Url::fromRoute('<none>'),
      '#attributes' => [
        'class' => ['btn', 'btn-primary', 'btn-icon-only', 'disabled'],
        'aria-label' => $this->t('Voir cette commune'),
      ],
    ];

    return $form;
  }

  /**
   * Callback from the autocomplete to update the link.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function updateLink(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $properties['vid'] = 'communes';
    $properties['name'] = $form_state->getValue('commune');

    $terms = $this->termStorage->loadByProperties($properties);
    if (empty($terms)) {
      return $response;
    }

    $commune = reset($terms);
    if (empty($commune)) {
      return $response;
    }

    $url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $commune->id()]);

    // Update the link.
    $response->addCommand(new InvokeCommand('#edit-go-link', 'attr', ['href', $url->toString()]));
    // Make the link enabled.
    $response->addCommand(new InvokeCommand('#edit-go-link', 'removeClass', ['disabled']));
    // Focus the link for accessibility.
    $response->addCommand(new InvokeCommand('#edit-go-link', 'focus'));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Do nothing.
  }

}
