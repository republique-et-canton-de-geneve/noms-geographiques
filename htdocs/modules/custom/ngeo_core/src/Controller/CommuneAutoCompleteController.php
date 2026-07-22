<?php

namespace Drupal\ngeo_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\TermStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Xss;

/**
 * Defines a route controller for watches autocomplete form elements.
 */
class CommuneAutoCompleteController extends ControllerBase {

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
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Handler for autocomplete request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The typed string.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The Communes similar to the string.
   */
  public function handleAutocomplete(Request $request) {
    $results = [];
    $input = $request->query->get('q');

    // Get the typed string, if it exists.
    if (!$input) {
      return new JsonResponse($results);
    }

    $input = Xss::filter($input);

    // Get published communes.
    $query = $this->termStorage->getQuery()
      ->condition('vid', 'communes')
      ->condition('name', $input, 'CONTAINS')
      ->condition('status', 1)
      ->groupBy('tid');

    $ids = $query->execute();
    $terms = $ids ? $this->termStorage->loadMultiple($ids) : [];

    foreach ($terms as $term) {
      $label = $term->getName();

      $results[] = [
      // We don't want an id here.
        'value' => $label,
        'label' => $label,
      ];
    }

    return new JsonResponse($results);
  }

}
