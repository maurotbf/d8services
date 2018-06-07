<?php

namespace Drupal\code_samples;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\DefaultNegotiator;

/**
 * Determines the default theme of the site.
 */
class CustomThemeNegotiator extends DefaultNegotiator {

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    $user = \Drupal::currentUser();

    if ($user->isAuthenticated()) {
      $messenger = \Drupal::messenger();
      $messenger->addWarning('Forcing all pages to be shown with the admin theme!');
      return $this->configFactory->get('system.theme')->get('default');
      return 'seven';
    } else {
      return $this->configFactory->get('system.theme')->get('default');
    }
  }

}