<?php

namespace Drupal\code_samples\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;

/**
 * Determines expiration time and performing logouts based on login status of current user.
 */
class ExpirationSample implements AccessInterface {

  /**
   * Checks current user status and logouts him if time expired.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(Route $route, AccountInterface $account) {
    // To get if the current user is logged in
    $actual_status = $account->isAuthenticated();

    // Returns the requirement for this given key (whether or not is a restricted page)
    $permission = $route->getRequirement('_permission');

    // If no permissions are required for the current page, then we don't alter the access response
    if ($permission === NULL) {
      return AccessResult::neutral();
    }

    // Calling a messenger object that will be used later. drupal_set_message() is deprecated now.
    $messenger = \Drupal::messenger();

    // Getting permission sets if the page needs them.
    $operator = 'AND';
    $split = explode(',', $permission);
    if (count($split) <= 1) {
      $split = explode('+', $permission);
      $operator = 'OR';
    }

    // User Session service invocation to get temporary data from a user session
    $tempstore = \Drupal::service('user.private_tempstore')->get('code_sample_settings');
    // We get the expiration time if available, from the temporary data stored in the user session
    $expiration_time = $tempstore->get('expiration_time');
    // Let's get the current time in order to compare it with the logged in time to check expiration
    $current_time = \Drupal::time()->getRequestTime();

    if (!isset($expiration_time) && $actual_status) {
      $expiration_time = strtotime("+1 minutes");
      $tempstore->set('expiration_time', $expiration_time);
    } else {
      if ($expiration_time < $current_time) {
        $messenger->addMessage('User session has expired.');
        $tempstore->set('expiration_time', null);
        user_logout();
        return AccessResult::forbidden();
      }
    }

    // If the account is authenticated, a message with the remaining time to expiration will appear
    if ($account->isAuthenticated()) {
      $remains = \Drupal::service('date.formatter')->format(
        $expiration_time - $current_time,
        'custom',
        'i \m\i\n\u\t\e\s, s \s\e\c\o\n\d\s'
      );
      $messenger->addMessage('User session expiration in: '.$remains);
    }

    return AccessResult::allowedIfHasPermissions($account, $split, $operator);
  }
}
