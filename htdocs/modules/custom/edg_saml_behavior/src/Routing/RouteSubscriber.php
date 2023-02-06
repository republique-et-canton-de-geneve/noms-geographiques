<?php

namespace Drupal\edg_saml_behavior\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {

    // Listing of user routes to block:
    $user_routes = [
      // In routing.yml of user:
      'entity.user.contact_form',
      'user.admin_create',
      'user.cancel_confirm',
      'user.login',
      'user.login.http',

      // Prefer le 404 rabbitHole:
      // 'user.page'.
      'user.pass',
      'user.pass.http',
      'user.register',
      'user.reset',
      'user.reset.form',
      'user.reset.login',

      // In UserRouteProvider (page user):
      // 'entity.user.canonical'.
      'entity.user.edit_form',
      'entity.user.cancel_form',
    ];

    $active_environment = \Drupal::config('environment_indicator.indicator');
    $title = $active_environment->get('name');

    // If the environment is not DEV or local, it is forbidden:
    if (!(isset($title) && (strcasecmp($title, 'dev') === 0 || strcasecmp($title, 'local') === 0))) {
      foreach ($user_routes as $user_route) {
        // Alter access control.
        if ($route = $collection->get($user_route)) {
          $route->setRequirement('_custom_access', '\Drupal\edg_saml_behavior\Access\UserFormAccessCheck::access');
        }
      }
    }

  }

}
