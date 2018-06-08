const forecast = require('./forecast');

module.exports = {
  init() {
    const layout = document.querySelector('.mdl-layout');
    const obfuscator = document.getElementsByClassName('mdl-layout__obfuscator')[0];
    Array.prototype.forEach.call(document.getElementsByClassName('mdl-navigation__link'), (elem) => {
      elem.onclick = (e) => {
        e.preventDefault();
        forecast.getForPlace(elem.dataset.forecastId);
        // if obfuscator is shown then we should hide the drawer
        if (obfuscator.classList.contains('is-visible')) {
          layout.MaterialLayout.toggleDrawer();
        }
      };
    });
    forecast.getForPlace(0); // shows all icons
  },
};
