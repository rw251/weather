const forecast = require('./forecast');

module.exports = {
  init() {
    let layout = document.querySelector('.mdl-layout');
    let obfuscator = document.getElementsByClassName('mdl-layout__obfuscator')[0];
    Array.prototype.forEach.call(document.getElementsByClassName('mdl-navigation__link'), (elem) => {
      elem.onclick = (e) => {
        e.preventDefault();
        forecast.getForPlace(elem.dataset.forecastId);
        // if obfuscator is shown then we should hide the drawer
        if (!obfuscator) [obfuscator] = document.getElementsByClassName('mdl-layout__obfuscator');
        if (obfuscator.classList.contains('is-visible')) {
          if (!layout) layout = document.querySelector('.mdl-layout');
          layout.MaterialLayout.toggleDrawer();
        }
      };
    });
    forecast.getForPlace(0); // shows all icons
  },
};
