<?php

namespace Drupal\edg_core\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\edg_core\Service\JsonService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to display GECH footer.
 *
 * @Block(
 *   id = "edg_core_gech_menu_footer",
 *   admin_label = @Translation("GECH menu header")
 * )
 */
class GechMenuFooterBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Json service.
   *
   * @var Drupal\edg_core\Service\JsonService
   */
  protected $jsonService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('edg_core.json_service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    JsonService $jsonService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->jsonService = $jsonService;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $url = 'https://www.ge.ch/blockasjson/menu_block/1';
    $response = $this->jsonService->getJson($url);

    // Fallback:
    if (empty($response)) {
      $response = '{"content":"\u003Cnav role=\u0022navigation\u0022 aria-labelledby=\u0022block-menupieddepagefonce-menu\u0022 id=\u0022block-menupieddepagefonce\u0022\u003E\n            \n  \u003Ch2 class=\u0022visually-hidden\u0022 id=\u0022block-menupieddepagefonce-menu\u0022\u003EMenu pied de page fonc\u00e9\u003C\/h2\u003E\n  \n\n        \n              \u003Cul\u003E\n              \u003Cli\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/conditions-generales\u0022 data-drupal-link-system-path=\u0022node\/640\u0022\u003EConditions g\u00e9n\u00e9rales\u003C\/a\u003E\n              \u003C\/li\u003E\n          \u003Cli\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/conditions-generales#translation\u0022 data-drupal-link-system-path=\u0022node\/640\u0022\u003ETraduction\u003C\/a\u003E\n              \u003C\/li\u003E\n          \u003Cli\u003E\n        \u003Ca href=\u0022https:\/\/ge.ch\/annuaire\/\u0022\u003EAnnuaire\u003C\/a\u003E\n              \u003C\/li\u003E\n          \u003Cli\u003E\n        \u003Ca href=\u0022https:\/\/ge.ch\/intranetetat\u0022\u003EIntranet Etat\u003C\/a\u003E\n              \u003C\/li\u003E\n        \u003C\/ul\u003E\n  \n\n\n  \u003C\/nav\u003E\n","title":"","description":""}';
    }

    $html = Json::decode($response);
    $build['#markup'] = $html['content'];
    $build['#cache']['tags'] = ['daily'];

    return $build;
  }

}
