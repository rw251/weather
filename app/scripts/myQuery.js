module.exports = {
  getJSON: (url, callback) => {
    const request = new XMLHttpRequest();
    request.open('GET', url, true);

    request.onload = () => {
      if (request.status >= 200 && request.status < 400) {
        // Success!
        const data = JSON.parse(request.responseText);
        return callback(null, data);
      }
      return callback(new Error('Response wasn\'t good.'));
    };

    request.onerror = (err) => {
      callback(err);
    };

    request.send();
  },

  ready: (fn) => {
    // already ready so just execute
    if (document.attachEvent ? document.readyState === 'complete' : document.readyState !== 'loading') {
      fn();
    } else { // not yet ready so add an event listener
      document.addEventListener('DOMContentLoaded', fn);
    }
  },
};
