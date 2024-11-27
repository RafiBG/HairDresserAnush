<?php

namespace Drupal\dark_mode_switch\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Dark Mode Switch' Block.
 *
 * @Block(
 *   id = "dark_mode_switch_block",
 *   admin_label = @Translation("Dark Mode Switch"),
 *   category = @Translation("Dark Mode"),
 * )
 */
class DarkModeSwitch extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The constructor.
   *
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(
    array $configuration,
          $plugin_id,
          $plugin_definition,
    ConfigFactoryInterface $config_factory
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $config = $this->configFactory->get('dark_mode_switch.settings');
    $dark_class = $config->get('dark_class');
    $parent_element = $config->get('parent_element');

    $build = [
      '#theme' => 'dark_mode_switch_block',
    ];

    $build['#attached']['library'][] = 'dark_mode_switch/dark_mode_switch';
    $build['#attached']['drupalSettings']['dark_mode_switch']['dark_class'] = $dark_class;
    $build['#attached']['drupalSettings']['dark_mode_switch']['parent_element'] = $parent_element;

    return $build;
  }

}
