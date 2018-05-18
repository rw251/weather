const forecast = require('./forecast');

module.exports = {
  init() {
    forecast.getForPlace(354775); // seahouses 354775, manchester 310013
  },
};
