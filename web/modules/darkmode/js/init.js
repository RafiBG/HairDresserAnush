/**
 * @file
 * Provides base JS for darkmode.
 */

((Drupal, once, drupalSettings) => {
  /**
   * Enables the dark mode widget.
   */
  Drupal.behaviors.darkModeWidget = {
    attach(context) {
      once('darkmode', 'body', context).forEach(() => {
        const options = {
          bottom: drupalSettings.darkmode.bottom,
          right: drupalSettings.darkmode.right,
          left: drupalSettings.darkmode.left,
          time: drupalSettings.darkmode.time,
          mixColor: drupalSettings.darkmode.mixColor,
          backgroundColor: drupalSettings.darkmode.backgroundColor,
          buttonColorDark: drupalSettings.darkmode.buttonColorDark,
          buttonColorLight: drupalSettings.darkmode.buttonColorLight,
          saveInCookies: drupalSettings.darkmode.saveInCookies,
          label: '🌓',
          autoMatchOsTheme: drupalSettings.darkmode.autoMatchOsTheme,
        };
        // Initialize the dark mode widget.
        /* global Darkmode */
        /* eslint no-undef: "error" */
        const darkmode = new Darkmode(options);
        darkmode.showWidget();
      });
    },
  };
})(Drupal, once, drupalSettings);
