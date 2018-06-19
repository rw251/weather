const forecast = require('./forecast');

module.exports = {
  init() {
    Notification.requestPermission((status) => {
      console.log('Notification permission status:', status);
    });

    let layout = document.querySelector('.mdl-layout');
    let obfuscator = document.getElementsByClassName('mdl-layout__obfuscator')[0];
    Array.prototype.forEach.call(document.getElementsByClassName('mdl-navigation__link'), (elem) => {
      elem.onclick = (e) => {
        e.preventDefault();
        Array.prototype.forEach.call(document.getElementsByClassName('mdl-navigation__link'), (x) => {
          x.classList.remove('selected');
        });
        elem.classList.add('selected');
        forecast.getForPlace(elem.dataset.forecastId);
        // if obfuscator is shown then we should hide the drawer
        if (!obfuscator) [obfuscator] = document.getElementsByClassName('mdl-layout__obfuscator');
        if (obfuscator.classList.contains('is-visible')) {
          if (!layout) layout = document.querySelector('.mdl-layout');
          layout.MaterialLayout.toggleDrawer();
        }
      };
    });
    forecast.getForPlace(352827); // shows all icons
  },
};
