(function (Drupal, drupalSettings, once) {
  const dataAttr = '[data-dark-mode-switch]';
  const darkClass = drupalSettings.dark_mode_switch.dark_class;
  const parentElement = drupalSettings.dark_mode_switch.parent_element;

  Drupal.behaviors.dark_mode_switch = {
    attach: function (context) {
      // be sure this is executed only once, on page load
      if (context === document) {
        // Check Dark Mode Switch Cookie.
        const elements = once('dark_mode_switch', dataAttr, context);
        elements.forEach(processingCallback);
        // Toggle Dark Mode Switch.
        document.querySelector(dataAttr).onchange = (event) => {
          document.querySelector(parentElement).classList.toggle(darkClass);
          localStorage.theme = event.target.checked
        };
      }
    }
  };

  function processingCallback() {
    let darkmodeState = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if ('theme' in localStorage) {
        darkmodeState = (localStorage.theme !== 'false');
      }
      document.querySelector(parentElement).classList.toggle(darkClass, darkmodeState);
      document.querySelector(dataAttr).checked = darkmodeState;
  }
})(Drupal, drupalSettings, once);
