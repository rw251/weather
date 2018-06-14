const $ = require('./scripts/myQuery');
const main = require('./scripts/main');

const App = {
  init: function init() {
    $.ready(() => {
      main.init();
    });
  },
};

module.exports = App;
