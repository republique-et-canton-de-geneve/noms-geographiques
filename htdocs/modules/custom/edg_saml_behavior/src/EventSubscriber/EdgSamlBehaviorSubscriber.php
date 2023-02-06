<?php

namespace Drupal\edg_saml_behavior\EventSubscriber;

use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * edg_saml_behavior event subscriber.
 */
class EdgSamlBehaviorSubscriber implements EventSubscriberInterface {

  use LoggerChannelTrait;

  /**
   * The module logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The currently logged in user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs event subscriber.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   */
  public function __construct(AccountProxyInterface $currentUser) {
    $this->logger = $this->getLogger('edg_saml_behavior');
    $this->currentUser = $currentUser;
  }

  /**
   * Enforce connection for any access through "cmsadmin" url.
   * Previous destination is keeped.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Response event.
   */
  public function onKernelRequest(RequestEvent $event) {

    $host = \Drupal::request()->getSchemeAndHttpHost();
    $current_uri = \Drupal::request()->getRequestUri();
    $route_name = Url::fromUserInput($current_uri)->getRouteName();

    // Try to catch any cmsadmin url : must be usefull for local, dev, rec and production
    if (preg_match("(http(|s):\/\/cmsadmin)", $host)) {

      // Only for anonymous user.
      if ($this->currentUser->isAnonymous()) {


        // Do not redirect during saml_login process.
        if ($route_name != "simplesamlphp_auth.saml_login") {
          // Redirect to saml_login route, keeping the original destination.
          $url = Url::fromRoute('simplesamlphp_auth.saml_login', ['destination' => $current_uri]);
          $response = new RedirectResponse($url->toString());
          $event->setResponse($response);
        }
      }
    }
    else {
      // SECURECO-4534 : bloquer l'accès à /saml_login sur l'url visiteur.
      // on redirige vers la page d'accueil.
      if ($route_name == "simplesamlphp_auth.saml_login") {
        $url = Url::fromRoute('<front>');
        $response = new RedirectResponse($url->toString());
        $event->setResponse($response);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      // Method 'onRequest' on [KernelEvents::REQUEST].
      // The priority must be just after the onKernelRequestAuthenticate as it
      // define the current user given to the construct method.
      KernelEvents::REQUEST => ['onKernelRequest', 299],
    ];
  }

}
