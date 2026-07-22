<?php

namespace Drupal\ngeo_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * Provides a 'sharing links' Block.
 *
 *  @params
 *  - entity_type: entity type like node or taxonomy_term.
 *  - id: entity id.
 *
 * @Block(
 *   id = "ngeo_sharing_links",
 *   admin_label = @Translation("Sharing Links Block"),
 * )
 */
class SharingLinksBlock extends BlockBase implements ContainerFactoryPluginInterface {
  use StringTranslationTrait;

  /**
   * EntityTypeManager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * RouteMatch service.
   *
   * @var Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_route_match'),
      $container->get('string_translation'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, RouteMatchInterface $routeMatch, TranslationInterface $string_translation) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entityTypeManager;
    $this->routeMatch = $routeMatch;
    $this->stringTranslation = $string_translation;

  }

  /**
   * {@inheritdoc}
   */
  public function build($param = []) {
    $build['#theme'] = 'ngeo_sharing_links';
    $permanentUrl = NULL;

    $options = ['absolute' => TRUE];
    if (isset($param['entity_type']) && isset($param['id'])) {
      $entity = $this->entityTypeManager->getStorage($param['entity_type'])->load($param['id']);
      $url_object = Url::fromRoute('entity.' . $param['entity_type'] . '.canonical', [$param['entity_type'] => $param['id']], $options);
      $permanentUrl = $url_object->toString();
    }

    if (empty($entity) || (!$entity instanceof NodeInterface && !$entity instanceof TermInterface)) {
      // Utile pour les urls qui ne sont pas de type node.
      $permanentUrl = Url::fromRoute('<current>', [], ['absolute' => TRUE])->toString();
    }

    $build['#url'] = $permanentUrl;
    $build['#links'] = [
      'facebook' => [
        'name'    => $this->t('Facebook'),
        'icon'    => 'fab fa-facebook-square fa-2x',
        'title'   => $this->t('Partagez sur Facebook'),
        'href'    => 'https://www.facebook.com/sharer/sharer.php?u=' . $permanentUrl,
        'more_attributes' => 'data-popup-width=700 data-popup-height=300',
      ],
      'twitter' => [
        'name'    => $this->t('Twitter'),
        'icon'    => 'fab fa-twitter-square fa-2x',
        'title'   => $this->t('Partagez sur Twitter'),
        'href'    => 'https://twitter.com/intent/tweet?text=' . $permanentUrl,
        'more_attributes' => 'data-popup-width=550 data-popup-height=220',
      ],
      'linkedin' => [
        'name'    => $this->t('Linkedin'),
        'icon'    => 'fab fa-linkedin fa-2x',
        'title'   => $this->t('Partagez sur Linkedin'),
        'href'    => 'http://www.linkedin.com/shareArticle?url=' . $permanentUrl,
        'more_attributes' => 'data-popup-width=600 data-popup-height=330',
      ],
      'lien_permanent' => [
        'name'    => $this->t('Lien permanent'),
        'icon'    =>
          [
            'fas fa-square fa-stack-2x',
            'fas fa-link fa-stack-1x fa-inverse',
          ],
        'title'   => $this->t('Lien permanent'),
        'href'    => '#',
        'more_attributes' => 'data-bs-toggle=collapse data-bs-target=#permanentLink aria-expanded=false aria-controls=permanentLink',
      ],
    ];

    return $build;
  }

}
