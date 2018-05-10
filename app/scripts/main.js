const forecast = require('./forecast');

module.exports = {
  init() {
    forecast.getForPlace(310013);
  },
};
