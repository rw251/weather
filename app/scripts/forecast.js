const $ = require('jquery');
const forecastItemTmpl = require('../templates/forecast-item.jade');

const weatherTypes = {
  0: { description: 'Clear night', icon: 'night-clear' },
  1: { description: 'Sunny day', icon: 'day-clear' },
  2: { description: 'Partly cloudy (night)', icon: 'night-partly-cloudy' },
  3: { description: 'Partly cloudy (day)', icon: 'day-partly-cloudy' },
  4: { description: 'Not used', icon: 'unknown' },
  5: { description: 'Mist', icon: 'mist' },
  6: { description: 'Fog', icon: 'fog' },
  7: { description: 'Cloudy', icon: 'cloudy' },
  8: { description: 'Overcast', icon: 'overcast' },
  9: { description: 'Light rain shower (night)', icon: 'night-shower' },
  10: { description: 'Light rain shower (day)', icon: 'day-shower' },
  11: { description: 'Drizzle', icon: 'drizzle' },
  12: { description: 'Light rain', icon: 'light-rain' },
  13: { description: 'Heavy rain shower (night)', icon: 'night-heavy-shower' },
  14: { description: 'Heavy rain shower (day)', icon: 'day-heavy-shower' },
  15: { description: 'Heavy rain', icon: 'heavy-rain' },
  16: { description: 'Sleet shower (night)', icon: 'night-sleet-shower' },
  17: { description: 'Sleet shower (day)', icon: 'day-sleet-shower' },
  18: { description: 'Sleet', icon: 'sleet' },
  19: { description: 'Hail shower (night)', icon: 'night-hail-shower' },
  20: { description: 'Hail shower (day)', icon: 'day-hail-shower' },
  21: { description: 'Hail', icon: 'hail' },
  22: { description: 'Light snow shower (night)', icon: 'night-light-snow-shower' },
  23: { description: 'Light snow shower (day)', icon: 'day-light-snow-shower' },
  24: { description: 'Light snow', icon: 'light-snow' },
  25: { description: 'Heavy snow shower (night)', icon: 'night-heavy-snow-shower' },
  26: { description: 'Heavy snow shower (day)', icon: 'day-heavy-snow-shower' },
  27: { description: 'Heavy snow', icon: 'heavy-snow' },
  28: { description: 'Thunder shower (night)', icon: 'night-heavy-shower' },
  29: { description: 'Thunder shower (day)', icon: 'day-heavy-shower' },
  30: { description: 'Thunder', icon: 'thunder' },
};

const processForecast = (forecast) => {
  const html = [];
  let inc = 0;
  forecast.Location.Period.forEach((day) => {
    day.Rep.forEach((period) => {
      html.push(forecastItemTmpl({
        date: day.value.substr(0, 10),
        time: `${period.$ / 60}:00`,
        // weatherType: weatherTypes[period.W].description,
        weatherType: weatherTypes[inc].description,
        // weatherIcon: weatherTypes[period.W].icon,
        weatherIcon: weatherTypes[inc].icon,
        feelsLike: period.F,
        temperature: period.T,
        uv: period.U,
        windSpeed: period.S,
        windDirection: period.D.toLowerCase(),
        probabilityRain: period.Pp,
      }));
      inc = (inc + 1) % 31;
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
