const $ = require('jquery');
const forecastItemTmpl = require('../templates/forecast-item.jade');

const weatherTypes = {
  0: 'Clear night',
  1: 'Sunny day',
  2: 'Partly cloudy (night)',
  3: 'Partly cloudy (day)',
  4: 'Not used',
  5: 'Mist',
  6: 'Fog',
  7: 'Cloudy',
  8: 'Overcast',
  9: 'Light rain shower (night)',
  10: 'Light rain shower (day)',
  11: 'Drizzle',
  12: 'Light rain',
  13: 'Heavy rain shower (night)',
  14: 'Heavy rain shower (day)',
  15: 'Heavy rain',
  16: 'Sleet shower (night)',
  17: 'Sleet shower (day)',
  18: 'Sleet',
  19: 'Hail shower (night)',
  20: 'Hail shower (day)',
  21: 'Hail',
  22: 'Light snow shower (night)',
  23: 'Light snow shower (day)',
  24: 'Light snow',
  25: 'Heavy snow shower (night)',
  26: 'Heavy snow shower (day)',
  27: 'Heavy snow',
  28: 'Thunder shower (night)',
  29: 'Thunder shower (day)',
  30: 'Thunder',
};

const processForecast = (forecast) => {
  const html = [];
  forecast.Location.Period.forEach((day) => {
    day.Rep.forEach((period) => {
      html.push(forecastItemTmpl({
        date: day.value.substr(0, 10),
        time: `${period.$ / 60}:00`,
        weatherType: weatherTypes[period.W],
        feelsLike: period.F,
        temperature: period.T,
        uv: period.U,
        windSpeed: period.S,
        windDirection: period.D.toLowerCase(),
        probabilityRain: period.Pp,
      }));
      console.log(`${day.value} - ${period.$ / 60}:00 ${weatherTypes[period.W]} ${period.T}℃(${period.F}℃) UV(${period.U}) ${period.S}${period.D} rain:${period.Pp}%`);
    });
  });
  $('#content').html(html.join(''));
};

module.exports = {
  getForPlace(placeId) {
    $
      .getJSON(`weather.php?place=${placeId}`)
      .done((data) => {
        console.log(data.SiteRep.Wx);
        processForecast(data.SiteRep.DV);
      })
      .fail((a, b, c) => {
        console.log(a, b, c);
      });
  },
};
