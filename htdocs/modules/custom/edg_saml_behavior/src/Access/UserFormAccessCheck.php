<?php

namespace Drupal\edg_saml_behavior\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * Custom access to user's pages.
 */
class UserFormAccessCheck {

  /**
   * Access method.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account.
   *
   * @return \Drupal\Core\Access\AccessResultForbidden
   *   A forbidden access result.
   */
  public function access(AccountInterface $account) {
    return AccessResult::forbidden();
  }

}
