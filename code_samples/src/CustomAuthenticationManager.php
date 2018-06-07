<?php

namespace Drupal\code_samples;

use Drupal\Core\Authentication\AuthenticationManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * Manager for authentication.
 *
 * On each request, let all authentication providers try to authenticate the
 * user. The providers are iterated according to their priority and the first
 * provider detecting credentials for its method wins. No further provider will
 * get triggered.
 *
 * If no provider set an active user then the user is set to anonymous.
 */
class CustomAuthenticationManager extends AuthenticationManager {

  /**
   * {@inheritdoc}
   */
  public function authenticate(Request $request) {
    $provider_id = $this->getProvider($request);
    $provider = $this->authCollector->getProvider($provider_id);

    if ($provider) {
      $messenger = \Drupal::messenger();
      $messenger->addWarning('Overriding the authenticate method. Now we could use external session management or any other mechanism.');
      return $provider->authenticate($request);
    }

    return NULL;
  }

}
