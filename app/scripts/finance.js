const $ = require('./myQuery');
const financeItemTmpl = require('../templates/finance-item.jade');

let cachedData = [];

const processData = (data) => {
  const html = [];
  data.forEach((d) => {
    if (d.updatedToday === '1') {
      if (+d.dayChange > 0) {
        d.change = 'up';
      } else if (+d.dayChange < 0) {
        d.change = 'down';
      }
    }
    html.push(financeItemTmpl(d));
  });
  document.getElementById('content').innerHTML = html.join('');
};

const preloadData = (data) => {
  cachedData = data;
};

module.exports = {
  display() {
    if (cachedData.length > 0) {
      processData(cachedData);
    } else {
      $.getJSON('finance.php', (err, data) => {
        if (err) console.log(err);
        else processData(data);
      });
    }
  },

  preload() {
    $.getJSON('finance.php', (err, data) => {
      if (err) console.log(err);
      else preloadData(data);
    });
  },
};
