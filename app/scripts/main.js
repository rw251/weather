const forecast = require('./forecast');
const finance = require('./finance');

module.exports = {
  init() {
    if (!!navigator.userAgent.match(/iphone|android|blackberry/ig) || false) {
      Notification.requestPermission((status) => {
        console.log('Notification permission status:', status);
      });
    }

    let layout = document.querySelector('.mdl-layout');
    let obfuscator = document.getElementsByClassName('mdl-layout__obfuscator')[0];
    Array.prototype.forEach.call(document.getElementsByClassName('side-link'), (elem) => {
      elem.onclick = (e) => {
        e.preventDefault();
        Array.prototype.forEach.call(document.getElementsByClassName('side-link'), (x) => {
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

    Array.prototype.forEach.call(document.getElementsByClassName('top-link'), (elem) => {
      elem.onclick = (e) => {
        e.preventDefault();
        Array.prototype.forEach.call(document.getElementsByClassName('top-link'), (x) => {
          x.classList.remove('selected');
        });
        switch (elem.dataset.link) {
          case 'finance':
            finance.display();
            break;
          default:
            forecast.getForPlace(352827);
            break;
        }
      };
    });

    forecast.getForPlace(352827); // shows all icons
    finance.preload();
  },
};
