<?php

/**
 * @file
 * Contains \Drupal\code_samples\EventSubscriber\RedirectSample.
 */

namespace Drupal\code_samples\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Sets the request format onto the request object.
 */
class RedirectSample implements EventSubscriberInterface {

  /**
   * Executes init code.
   */
  public function customRequest() {
    // Sample URL address to point to when page not found.
    $path = 'http://www.404notfound.fr';

    // First case: Page not found
    $request = \Drupal::request();
    $page_code = $request->attributes->get('_route');

    if ($page_code == 'system.404') {
      // Disabling cache for this request, this way we prevent Drupal to cache
      // this request next time if the page is now found.
      \Drupal::service('page_cache_kill_switch')->trigger();

      // Preparing response
      $response = new RedirectResponse($path);
      $response->setPrivate();
      $response->setMaxAge(0);
      $response->setSharedMaxAge(0);
      $response->headers->addCacheControlDirective('must-revalidate', true);
      $response->headers->addCacheControlDirective('no-store', true);
      $response->send();
    }
  }

  /**
   * Registers the methods in this class that should be listeners.
   *
   * @return array
   *   An array of event listener definitions.
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('customRequest');
    return $events;
  }

}