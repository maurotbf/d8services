<?php
/**
 * @file
 * Contains Drupal\code_samples\CodeSamplesServiceProvider
 */

namespace Drupal\code_samples;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Modifies two sample services from core.
 */
class CodeSamplesServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Overrides authentication class to test custom authentication capabilities.
    $definition = $container->getDefinition('authentication');
    $definition->setClass('Drupal\code_samples\CustomAuthenticationManager');

    // Overrides theme negotiation class to test custom theme negotiation.
    $definition = $container->getDefinition('theme.negotiator.default');
    $definition->setClass('Drupal\code_samples\CustomThemeNegotiator');
  }
}
