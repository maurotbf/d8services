<?php

/**
 * @file
 * Contains \Drupal\code_samples\Plugin\Block\CodeSampleBlock.
 */

namespace Drupal\code_samples\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\user\PermissionHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'D8 Code Samples' block.
 *
 * @Block(
 *   id = "code_samples_block",
 *   admin_label = @Translation("Code Samples Block"),
 *   category = @Translation("Custom")
 * )
 */
class CodeSamplesBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * @var PermissionHandler
   */
  protected $perms;
  protected $current_user;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PermissionHandler $perms, AccountProxy $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->perms = $perms;
    $this->current_user = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('user.permissions'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $user_perms = $this->perms->getPermissions();

    if ($this->current_user->isAuthenticated()) {
      $output = "<ul>";
      foreach ($user_perms as $perm) {
        $output .= '<li>'.$perm['title']->render().'</li>';
      }
      $output .= "</ul>";
    } else {
      $output = 'Anonymous';
    }

    return array(
      '#type' => 'markup',
      '#markup' => 'Perm listing: '.$output,
    );
  }

}
