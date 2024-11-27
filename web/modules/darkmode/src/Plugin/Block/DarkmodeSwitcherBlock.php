<?php

namespace Drupal\darkmode\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds Darkmode switcher block.
 *
 * @package Drupal\darkmode\Plugin\Block
 */
#[Block(
    id: "darkmode_switcher",
    admin_label: new TranslatableMarkup("Darkmode Switcher"),
)]
class DarkmodeSwitcherBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Constructor for Darkmode Switcher block.
   *
   * @param array $configuration
   *   Configuration array.
   * @param string $plugin_id
   *   Plugin ID.
   * @param mixed $plugin_definition
   *   Plugin definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The factory for configuration objects.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, protected ConfigFactoryInterface $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $config = $this->config->get('darkmode.config');
    $build['content'] = [
      '#attached' => [
        'drupalSettings' => [
          'darkmode' => [
            'bottom' => $config->get('bottom'),
            'right' => $config->get('right'),
            'left' => $config->get('left'),
            'time' => $config->get('time'),
            'mixColor' => $config->get('mix_color'),
            'backgroundColor' => $config->get('background_color'),
            'buttonColorDark' => $config->get('button_color_dark'),
            'buttonColorLight' => $config->get('button_color_light'),
            'saveInCookies' => $config->get('save_in_cookies'),
            'autoMatchOsTheme' => $config->get('auto_match_os_theme'),
          ],
        ],
        'library' => [
          'darkmode/initiator',
          'darkmode/darkmodecss',
        ],
      ],
    ];
    return $build;
  }

}
