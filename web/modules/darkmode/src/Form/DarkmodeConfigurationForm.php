<?php

namespace Drupal\darkmode\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure settings for the Darkmode Switcher block.
 */
class DarkmodeConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'darkmode_options_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'darkmode.options_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('darkmode.config');

    $form['bottom'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Bottom'),
      '#default_value' => $config->get('bottom') ?? '64px',
      '#description' => $this->t('Enter number of pixel for Bottom or unset'),
    ];
    $form['right'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Right'),
      '#default_value' => $config->get('right') ?? '32px',
      '#description' => $this->t('Enter number of pixel for Right or unset'),
    ];
    $form['left'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Left'),
      '#default_value' => $config->get('left') ?? 'unset',
      '#description' => $this->t('Enter number of pixel for Left or unset'),
    ];
    $form['time'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Time'),
      '#default_value' => $config->get('time') ?? '0.5s',
      '#description' => $this->t('Enter number of Seconds'),
    ];
    $form['mix_color'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mix Color'),
      '#default_value' => $config->get('mix_color') ?? '#fff',
      '#description' => $this->t('Enter the color code Ex: #fff'),
    ];
    $form['background_color'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Background Color'),
      '#default_value' => $config->get('background_color') ?? '#fff',
      '#description' => $this->t('Enter the color code Ex: #fff'),
    ];
    $form['button_color_dark'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button Color Dark'),
      '#default_value' => $config->get('button_color_dark') ?? '#100f2c',
      '#description' => $this->t('Enter the color code Ex: #fff'),
    ];
    $form['button_color_light'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button Color Light'),
      '#default_value' => $config->get('button_color_light') ?? '#fff',
      '#description' => $this->t('Enter the color code Ex: #fff'),
    ];
    $form['save_in_cookies'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Save in Cookies'),
      '#default_value' => $config->get('save_in_cookies') ?? FALSE,
    ];
    $form['auto_match_os_theme'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Auto Match os Theme'),
      '#default_value' => $config->get('auto_match_os_theme') ?? TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->configFactory->getEditable('darkmode.config')
      ->set('bottom', $form_state->getValue('bottom'))
      ->set('right', $form_state->getValue('right'))
      ->set('left', $form_state->getValue('left'))
      ->set('time', $form_state->getValue('time'))
      ->set('mix_color', $form_state->getValue('mix_color'))
      ->set('background_color', $form_state->getValue('background_color'))
      ->set('button_color_dark', $form_state->getValue('button_color_dark'))
      ->set('button_color_light', $form_state->getValue('button_color_light'))
      ->set('save_in_cookies', (bool) $form_state->getValue('save_in_cookies'))
      ->set('auto_match_os_theme', (bool) $form_state->getValue('auto_match_os_theme'))
      ->save();
  }

}
