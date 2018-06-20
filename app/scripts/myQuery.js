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

  /**
   * Makes an ajax get
   * @param {string} url The url to get
   * @param {function} callback The callback on completion
   * @returns {void}
   */
  get(url, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = () => {
      callback(null, JSON.parse(xhr.responseText));
    };
    xhr.onabort = () => {
      callback(new Error('aborted'));
    };
    xhr.onerror = () => {
      callback(new Error('error'));
    };
    xhr.send();
  },

  /**
   * Makes an ajax post
   * @param {string} url The url to post to
   * @param {Object} data The data to post as a js object
   * @param {function} callback The callback on completion
   * @returns {void}
   */
  post(url, data, callback) {
    let xhr = new XMLHttpRequest();
    if (!('withCredentials' in xhr)) xhr = new XDomainRequest(); // fix IE8/9
    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = () => {
      callback(null, JSON.parse(xhr.responseText));
    };
    xhr.onabort = () => {
      callback(new Error('aborted'));
    };
    xhr.onerror = () => {
      callback(new Error('error'));
    };
    const body = Object.keys(data).map(key => `${key}=${data[key]}`).join('&');
    xhr.send(body);
  },
};
