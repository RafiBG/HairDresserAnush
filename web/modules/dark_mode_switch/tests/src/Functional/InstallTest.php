<?php

namespace Drupal\Tests\dark_mode_switch\Functional;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Test the 'dark_mode_switch' module install without errors.
 *
 * @group dark_mode_switch
 */
class InstallTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['dark_mode_switch'];

  /**
   * Assert that the 'dark_mode_switch' module installed correctly.
   */
  public function testModuleInstalls() {
    // If we get here, then the module was successfully installed during the
    // setUp phase without throwing any Exceptions. Assert that TRUE is true,
    // so at least one assertion runs, and then exit.
    $this->assertTrue(TRUE, 'Module installed correctly.');
  }

}
