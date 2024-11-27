<?php

namespace Drupal\dark_mode_switch\Form;

use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Dark mode switch settings form.
 */
class DarkModeSwitchSettings extends ConfigFormBase {

  /**
   * Drupal\Core\Extension\ThemeHandlerInterface definition.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ThemeHandlerInterface $theme_handler) {
    parent::__construct($config_factory);
    $this->themeHandler = $theme_handler;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('theme_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'dark_mode_switch.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dark_mode_switch_form';
  }

  /**
   * Gets a list of active themes without hidden ones.
   *
   * @return array[]
   *   An array with all compatible active themes.
   */
  private function getThemeList() {
    $config = $this->config('dark_mode_switch.settings')->get('dark_mode');
    $themes_list = [];
    $themes = $this->themeHandler->listInfo();
    foreach ($themes as $theme) {
      $theme_name = $theme->getName();
      if (!empty($theme->info['hidden'])) {
        continue;
      }
      $themes_list[$theme_name] = [
        'theme_name' => $theme->info['name'],
        'start_time' => !empty($config[$theme_name]['start_time']) ?: "",
        'end_time' => !empty($config[$theme_name]['end_time']) ?: "",
      ];
    }
    return $themes_list;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dark_mode_switch.settings');
    $themes = $this->getThemeList();
    $selected_theme = $config->get('active_theme');

    if (!empty($selected_theme)) {
      $this->messenger()
        ->addMessage($this->t("<b> @theme </b> will activate at time <b>@start_time</b> and deactivate at time <b>@end_time</b> every day.", [
          '@theme' => $themes[$selected_theme]['theme_name'],
          '@start_time' => date('h:i A', strtotime($themes[$selected_theme]['start_time'])),
          '@end_time' => date('h:i A', strtotime($themes[$selected_theme]['end_time'])),
        ]));
    }

    $theme_options = [
      '' => $this->t("- Select Theme -"),
    ];
    $form['dark_mode'] = [
      '#type' => 'table',
      '#title' => $this->t('Dark mode processing order'),
      '#header' => [
        $this->t('Theme Activation Time'),
        $this->t('Theme Deactivation Time'),
        $this->t('Theme Name'),
      ],
      '#empty' => $this->t('There are no items yet. Add roles.'),
    ];

    foreach ($themes as $machine_name => $info) {
      $form['dark_mode'][$machine_name]['start_time'] = [
        '#type' => 'time',
        '#default_value' => $info['start_time'],
      ];
      $form['dark_mode'][$machine_name]['end_time'] = [
        '#type' => 'time',
        '#default_value' => $info['end_time'],
      ];
      $form['dark_mode'][$machine_name]['theme'] = [
        '#type' => '#markup',
        '#markup' => $info['theme_name'],
      ];
      $theme_options[$machine_name] = $info['theme_name'];
    }

    $form['active_theme'] = [
      '#type' => 'select',
      '#title' => $this->t("Dark mode theme"),
      '#default_value' => !empty($selected_theme) ? $selected_theme : '',
      '#options' => $theme_options,
      '#description' => $this->t("This theme will be activated as per your selected time duration above. If you have not selected any theme, then no theme will be activated."),
    ];

    $form['dark_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Dark mode classname to append'),
      '#description' => $this->t('Enter the classname to append to the parent element when dark mode is enabled.'),
      '#required' => TRUE,
      '#default_value' => $config->get('dark_class'),
    ];

    $form['parent_element'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Parent element to bind to'),
      '#description' => $this->t('The jQuery element selector you want the dark mode class to appear on. This could be an element, like body or html, or a class selector like .content.'),
      '#required' => TRUE,
      '#default_value' => $config->get('parent_element'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#button_type' => 'primary',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $themes = $this->getThemeList();
    // Check time for selected can't be empty.
    $selected_theme = $form_state->getValue('active_theme');
    if (!empty($selected_theme)) {
      if (empty($form_state->getValue('dark_mode')[$selected_theme]['start_time'])) {
        $form_state->setErrorByName("dark_mode[$selected_theme][start_time]", $this->t("Start time can't be empty for @theme", ['@theme' => $themes[$selected_theme]['theme_name']]));
      }
      if (empty($form_state->getValue('dark_mode')[$selected_theme]['end_time'])) {
        $form_state->setErrorByName("dark_mode[$selected_theme][end_time]", $this->t("End time can't be empty for @theme", ['@theme' => $themes[$selected_theme]['theme_name']]));
      }
      // Validate Start and End time.
      // If start time is selected than end time can't be empty.
      // If end time is selected than start time can't be empty.
      foreach ($themes as $machine_name => $label) {
        $start_time = $form_state->getValue('dark_mode')[$machine_name]['start_time'];
        $end_time = $form_state->getValue('dark_mode')[$machine_name]['end_time'];
        if ((!empty($start_time) && empty($end_time))) {
          $form_state->setErrorByName("dark_mode[$machine_name][end_time]", $this->t("End time can't be empty for @theme", ['@theme' => $label['theme_name']]));
        }
        elseif ((!empty($end_time) && empty($start_time))) {
          $form_state->setErrorByName("dark_mode[$machine_name][start_time]", $this->t("Start time can't be empty for @theme", ['@theme' => $label['theme_name']]));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $themes = $this->getThemeList();
    $theme_data = [];
    foreach ($themes as $machine_name => $label) {
      $theme_data[$machine_name] = $form_state->getValue('dark_mode')[$machine_name];
    }
    $this->config('dark_mode_switch.settings')
      ->set('dark_mode', $theme_data)
      ->set('active_theme', $form_state->getValue('active_theme'))
      ->set('dark_class', $form_state->getValue('dark_class'))
      ->set('parent_element', $form_state->getValue('parent_element'))
      ->save();
    $this->messenger()->addMessage($this->t('Configuration saved.'));
  }

}
