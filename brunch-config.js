module.exports = {
  // See http://brunch.io for documentation.
  files: {
    javascripts: {
      joinTo: {
        'libraries.js': /^(?!app\/)/,
        'app.js': /^app\//,
      },
      order: {
        before: [/jquery/],
      },
    },
    stylesheets: {
      joinTo: 'app.css',
    },
    templates: {
      joinTo: 'app.js',
    },
  },

  server: { command: 'php -S 0.0.0.0:8180 -t public -c php.ini' },

  plugins: {
    autoReload: {
      port: [8081, 8082],
    },
    appcache: {
      ignore: /[/][.]|php/,
    },
  },
};
