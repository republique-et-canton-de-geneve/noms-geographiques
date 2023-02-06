<?php

namespace Drupal\ngeo_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Link;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides a breadcrumb.
 *
 * @Block(
 *   id = "ngeo_breadcrumb",
 *   admin_label = @Translation("Breadcrumb Block"),
 * )
 */
class BreadcrumbBlock extends BlockBase implements ContainerFactoryPluginInterface {
  use StringTranslationTrait;

  /**
   * The Request service instance.
   *
   * @var \Symfony\Component\HttpFoundation\Request|null
   */
  protected $request;

  /**
   * The first header value or default value of the request.
   *
   * @var string|null
   */
  protected $referer;

  /**
   * The CurrentRouteMatch service instance.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * The path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The path validator service.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $pathValidator;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack'),
      $container->get('current_route_match'),
      $container->get('path.matcher'),
      $container->get('config.factory'),
      $container->get('path.validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(
                              array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              RequestStack $requestStack,
                              CurrentRouteMatch $currentRouteMatch,
                              PathMatcherInterface $path_matcher,
                              ConfigFactoryInterface $config_factory,
                              PathValidatorInterface $path_validator) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->request = $requestStack->getCurrentRequest();
    $this->referer = $this->request->headers->get('referer');
    $this->currentRouteMatch = $currentRouteMatch;
    $this->pathMatcher = $path_matcher;
    $this->configFactory = $config_factory;
    $this->pathValidator = $path_validator;
  }

  /**
   * {@inheritdoc}
   */
  public function build($param = []) {

    // Homepage case: only the slogan.
    if ($this->pathMatcher->isFrontPage()) {
      $slogan = $this->configFactory->get('system.site')->get('slogan');
      $build['#markup'] = Markup::create('<span class="d-lg-none d-xl-block">' . $slogan . ' </span>');
      return $build;
    }

    // If the previous page was internal:
    if ($this->isRefererDrupalRouted()) {

      if (\Drupal::routeMatch()->getRouteName() === 'search.view') {
        // Search page case: Force back to homepage.
        $linkMarkup = Link::fromTextAndUrl($this->t("Retour à l'accueil"), Url::fromRoute('<front>'))->toString();
      }
      else {
        // Other cases.
        $urlParsed = parse_url($this->referer);
        $basePath = $this->request->getBasePath();

        // Keep only path from the URL.
        $urlPath = str_replace($basePath, '', $urlParsed['path']);
        $urlObject = $this->pathValidator->getUrlIfValid($urlPath);
        // Get the route name of the previous page:
        $routeName = $urlObject->getRouteName();

        if ($routeName === 'entity.taxonomy_term.canonical') {
          // Comes from a commune:
          $text = $this->t("Retour à la commune");
          $linkMarkup = Markup::create('<a href="' . $this->referer . '">' . $text . '</a>');
        }
        else if ($routeName === 'search.view') {
          // Comes from the search:
          $text = $this->t("Retour aux résultats de recherche");
          $linkMarkup = Markup::create('<a href="' . $this->referer . '">' . $text . '</a>');
        }
        else  {
          $linkMarkup = Link::fromTextAndUrl($this->t("Retour à l'accueil"), Url::fromRoute('<front>'))->toString();
        }
        
        $build['#cache']['contexts'][] = 'headers:referer';
      }
    }
    else {
      // External pages: back to clean search.
      $linkMarkup = Link::fromTextAndUrl($this->t("Retour à la recherche"), Url::fromRoute('view.search.page_recherche'))->toString();
    }

    // Add the arrow icon before the back link.
    $build['#markup'] = Markup::create('<span class="fas fa-arrow-left"></span>') . $linkMarkup;
    return $build;
  }

  /**
   * Check if referer URL match a Drupal route.
   *
   * @return bool
   */
  private function isRefererDrupalRouted() {
    if ($this->referer) {

      /** @var array $urlParsed */
      $urlParsed = parse_url($this->referer);

      $basePath = $this->request->getBasePath();
      $urlPath = str_replace($basePath, '', $urlParsed['path']);

      /** @var Url $refererUrl */
      $refererUrl = Url::fromUri('internal:' . $urlPath);

      if ($refererUrl->isRouted()) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
