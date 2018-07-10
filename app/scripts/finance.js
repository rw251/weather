const $ = require('./myQuery');
const financeItemTmpl = require('../templates/finance-item.jade');

const processData = (data) => {
  const html = [];
  data.forEach((d) => {
    html.push(financeItemTmpl(d));
  });
  document.getElementById('content').innerHTML = html.join('');
};

module.exports = {
  display() {
    $.getJSON('finance.php', (err, data) => {
      if (err) console.log(err);
      else processData(data);
    });
  },
};
