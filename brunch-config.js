module.exports = {
  // See http://brunch.io for documentation.
  paths: { public: 'public/weather' },
  files: {
    javascripts: {
      joinTo: {
        'libraries.js': /^(?!app\/)/,
        'app.js': /^app\//,
      },
    },
    stylesheets: { joinTo: 'app.css' },
    templates: { joinTo: 'app.js' },
  },

  server: { command: 'php -S 0.0.0.0:8180 -t public/weather -c php.ini' },

  plugins: {
    autoReload: { port: [8081, 8082] },
    appcache: { ignore: /[/][.]|php/ },
  },
};
