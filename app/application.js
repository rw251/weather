const $ = require('./scripts/myQuery');
const main = require('./scripts/main');

require('./scripts/auth')(() => {
  console.log('client authentication');
}, () => {
  console.log('server authentication');
});

const App = {
  init: function init() {
    $.ready(() => {
      main.init();
    });
  },
};

module.exports = App;
