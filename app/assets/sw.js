self.addEventListener('install', function(e) {
  e.waitUntil(
    caches.open('weather').then(function(cache) {
      return cache.addAll([
        '/',
        '/index.html',
        '/images/both-cloudy.svg',
        '/images/both-overcast.svg',
        '/images/day-light-snow-shower.svg',
        '/images/night-heavy-snow-shower.svg',
        '/images/both-drizzle.svg',
        '/images/both-sleet.svg',
        '/images/day-partly-cloudy.svg',
        '/images/night-light-snow-shower.svg',
        '/images/both-hail.svg',
        '/images/both-thunder.svg',
        '/images/day-shower.svg',
        '/images/night-partly-cloudy.svg',
        '/images/both-heavy-rain.svg',
        '/images/both-unknown.svg',
        '/images/day-sleet-shower.svg',
        '/images/night-shower.svg',
        '/images/both-heavy-snow.svg',
        '/images/day.svg',
        '/images/day-thunder-shower.svg',
        '/images/night-sleet-shower.svg',
        '/images/both-light-rain.svg',
        '/images/day-hail-shower.svg',
        '/images/night.svg',
        '/images/night-thunder-shower.svg',
        '/images/both-light-snow.svg',
        '/images/day-heavy-shower.svg',
        '/images/night-hail-shower.svg',
        '/images/both-mist.svg',
        '/images/day-heavy-snow-shower.svg',
        '/images/night-heavy-shower.svg'
      ]);
    })
  );
 });

self.addEventListener('fetch', function(e) {
  e.respondWith(
    caches.match(e.request).then(function(response) {
      return response || fetch(e.request);
    })
  );
});