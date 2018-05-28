const $ = require('jquery');
const forecastItemTmpl = require('../templates/forecast-item.jade');

const weatherTypes = {
  0: { description: 'Clear night', icon: 'night-clear' },
  1: { description: 'Sunny day', icon: 'day-clear' },
  2: { description: 'Partly cloudy (night)', icon: 'night-partly-cloudy' },
  3: { description: 'Partly cloudy (day)', icon: 'day-partly-cloudy' },
  4: { description: 'Not used', icon: '' },
  5: { description: 'Mist', icon: 'mist' },
  6: { description: 'Fog', icon: 'fog' },
  7: { description: 'Cloudy', icon: 'cloudy' },
  8: { description: 'Overcast', icon: 'overcast' },
  9: { description: 'Light rain shower (night)', icon: 'night-shower' },
  10: { description: 'Light rain shower (day)', icon: 'day-shower' },
  11: { description: 'Drizzle', icon: 'wi-showers' },
  12: { description: 'Light rain', icon: '' },
  13: { description: 'Heavy rain shower (night)', icon: '' },
  14: { description: 'Heavy rain shower (day)', icon: '' },
  15: { description: 'Heavy rain', icon: '' },
  16: { description: 'Sleet shower (night)', icon: '' },
  17: { description: 'Sleet shower (day)', icon: '' },
  18: { description: 'Sleet', icon: '' },
  19: { description: 'Hail shower (night)', icon: '' },
  20: { description: 'Hail shower (day)', icon: '' },
  21: { description: 'Hail', icon: '' },
  22: { description: 'Light snow shower (night)', icon: '' },
  23: { description: 'Light snow shower (day)', icon: '' },
  24: { description: 'Light snow', icon: '' },
  25: { description: 'Heavy snow shower (night)', icon: '' },
  26: { description: 'Heavy snow shower (day)', icon: '' },
  27: { description: 'Heavy snow', icon: '' },
  28: { description: 'Thunder shower (night)', icon: '' },
  29: { description: 'Thunder shower (day)', icon: '' },
  30: { description: 'Thunder', icon: '' },
};

const processForecast = (forecast) => {
  const html = [];
  forecast.Location.Period.forEach((day) => {
    day.Rep.forEach((period) => {
      html.push(forecastItemTmpl({
        date: day.value.substr(0, 10),
        time: `${period.$ / 60}:00`,
        weatherType: weatherTypes[period.W].description,
        weatherIcon: weatherTypes[period.W].icon,
        feelsLike: period.F,
        temperature: period.T,
        uv: period.U,
        windSpeed: period.S,
        windDirection: period.D.toLowerCase(),
        probabilityRain: period.Pp,
      }));
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
