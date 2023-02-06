<?php

namespace Drupal\edg_core\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\edg_core\Service\JsonService;

/**
 * Provides a block to display GECH header.
 *
 * @Block(
 *   id = "edg_core_gech_menu_header",
 *   admin_label = @Translation("GECH menu header")
 * )
 */
class GechMenuHeaderBlock extends BlockBase implements ContainerFactoryPluginInterface {

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

    $url = 'https://www.ge.ch/blockasjson/menu_block/header-full';
    $response = $this->jsonService->getJson($url);

    // Fallback:
    if (empty($response)) {
      $response = '{"content":"\u003Cdiv id=\u0022menu-wrapper\u0022\u003E\n    \u003Cdiv class=\u0022container\u0022\u003E\n        \u003Cdiv class=\u0022row\u0022\u003E\n                        \u003Cdiv class=\u0022menu-col col-12 col-md-6 col-lg-3\u0022\u003E\n                \u003Cdiv class=\u0022menu-title\u0022\u003E\n  A la une\n\u003C\/div\u003E\n\u003Cul class=\u0022list-group\u0022\u003E\n            \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/publication?type=460\u0022\u003EActualit\u00e9s\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/publication?All=\u0026amp;organisation=497\u0022\u003ED\u00e9cisions du Conseil d\u0026#039;\u00c9tat\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/dossier\u0022\u003EDossiers\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/evenement\u0022\u003EEv\u00e9nements\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/teaser\u0022\u003ETeasers\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/blog\u0022\u003EBlogs\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/publication?type=498\u0022\u003ENewsletters\u003C\/a\u003E\n      \u003C\/li\u003E\n      \u003C\/ul\u003E\n\n\n            \u003C\/div\u003E\n                        \u003Cdiv class=\u0022menu-col col-12 col-md-12 col-lg-6 order-lg-2 order-md-3\u0022\u003E\n              \u003Cdiv class=\u0022menu-title\u0022\u003E\n  D\u00e9marches\n\u003C\/div\u003E\n\u003Cdiv class=\u0022row\u0022\u003E\n      \u003Cdiv class=\u0022double-list col-md-6 col-12\u0022\u003E\n      \u003Cul class=\u0022list-group\u0022\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/aides-financieres-argent-impots\u0022\u003EAides financi\u00e8res, argent et imp\u00f4ts\u003C\/a\u003E\n          \u003C\/li\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/arriver-geneve-installer-partir\u0022\u003EArriver \u00e0 Gen\u00e8ve, s\u0026#039;installer, partir\u003C\/a\u003E\n          \u003C\/li\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/construire-se-loger\u0022\u003EConstruire et se loger\u003C\/a\u003E\n          \u003C\/li\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/ecoles-formations\u0022\u003EEcoles et formations\u003C\/a\u003E\n          \u003C\/li\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/emploi-travail-chomage\u0022\u003EEmploi, travail, ch\u00f4mage\u003C\/a\u003E\n          \u003C\/li\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/entreprises\u0022\u003EEntreprises\u003C\/a\u003E\n          \u003C\/li\u003E\n              \u003C\/ul\u003E\n    \u003C\/div\u003E\n      \u003Cdiv class=\u0022double-list col-md-6 col-12\u0022\u003E\n      \u003Cul class=\u0022list-group\u0022\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/etat-civil-droits-civiques\u0022\u003EEtat civil et droits civiques\u003C\/a\u003E\n          \u003C\/li\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/mobilite\u0022\u003EMobilit\u00e9\u003C\/a\u003E\n          \u003C\/li\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/police-securite-reglement-conflits\u0022\u003EPolice, s\u00e9curit\u00e9 et r\u00e8glement des conflits\u003C\/a\u003E\n          \u003C\/li\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/sante-soins-handicaps\u0022\u003ESant\u00e9, soins et handicaps\u003C\/a\u003E\n          \u003C\/li\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/territoire-environnement\u0022\u003ETerritoire et environnement\u003C\/a\u003E\n          \u003C\/li\u003E\n                  \u003Cli class=\u0022list-group-item\u0022\u003E\n            \u003Ca href=\u0022https:\/\/***REMOVED***\/parcourir\/vivre-dans-canton\u0022\u003EVivre dans le canton\u003C\/a\u003E\n          \u003C\/li\u003E\n              \u003C\/ul\u003E\n    \u003C\/div\u003E\n  \u003C\/div\u003E\n\n\n            \u003C\/div\u003E\n                        \u003Cdiv class=\u0022menu-col col-12 col-md-6 col-lg-3 order-lg-3 order-md-2\u0022\u003E\n              \u003Cdiv class=\u0022menu-title\u0022\u003E\n  Organisation et documents\n\u003C\/div\u003E\n\u003Cul class=\u0022list-group\u0022\u003E\n            \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/organisation\u0022\u003EAutorit\u00e9s\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/ge.ch\/annuaire\u0022\u003EAnnuaire\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/publication\u0022\u003EPublications\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/document\/statistiques-cantonales\u0022\u003EStatistiques\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/silgeneve.ch\/legis\/index.aspx\u0022\u003EL\u00e9gislation\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/fao.ge.ch\u0022\u003EFeuille d\u0026#039;avis officielle\u003C\/a\u003E\n      \u003C\/li\u003E\n          \u003Cli class=\u0022list-group-item\u0022\u003E\n        \u003Ca href=\u0022https:\/\/***REMOVED***\/offres-emploi-etat-geneve\/liste-offres\u0022\u003EOffres d\u0026#039;emploi\u003C\/a\u003E\n      \u003C\/li\u003E\n      \u003C\/ul\u003E\n\n\n\n\n            \u003C\/div\u003E\n\n        \u003C\/div\u003E\n        \u003Ca href=\u0022#main-content\u0022 class=\u0022navbar-toggler visually-hidden focusable skip-link order-4\u0022\u003E\n            Quitter le menu\n        \u003C\/a\u003E\n    \u003C\/div\u003E\n\u003C\/div\u003E\n\n\n\n\n","title":"","description":""}';
    }

    $html = Json::decode($response);
    $build['#markup'] = $html['content'];
    $build['#cache']['tags'] = ['daily'];

    return $build;
  }

}
