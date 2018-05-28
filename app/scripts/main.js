const forecast = require('./forecast');

module.exports = {
  init() {
    forecast.getForPlace(310013); // seahouses 354775, manchester 310013
  },
};
