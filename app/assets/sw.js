const currentCache = 'static-v1.0.1';
const expectedCaches = [currentCache];

self.addEventListener('install', function(e) {
  self.skipWaiting();

  e.waitUntil(
    caches.open(currentCache).then(function(cache) {
      return cache.addAll([
        '',
        'index.html',
        'images/both-cloudy.svg',
        'images/both-overcast.svg',
        'images/day-light-snow-shower.svg',
        'images/night-heavy-snow-shower.svg',
        'images/both-drizzle.svg',
        'images/both-sleet.svg',
        'images/day-partly-cloudy.svg',
        'images/night-light-snow-shower.svg',
        'images/both-hail.svg',
        'images/both-thunder.svg',
        'images/day-shower.svg',
        'images/night-partly-cloudy.svg',
        'images/both-heavy-rain.svg',
        'images/both-unknown.svg',
        'images/day-sleet-shower.svg',
        'images/night-shower.svg',
        'images/both-heavy-snow.svg',
        'images/day.svg',
        'images/day-thunder-shower.svg',
        'images/night-sleet-shower.svg',
        'images/both-light-rain.svg',
        'images/day-hail-shower.svg',
        'images/night.svg',
        'images/night-thunder-shower.svg',
        'images/both-light-snow.svg',
        'images/day-heavy-shower.svg',
        'images/night-hail-shower.svg',
        'images/both-mist.svg',
        'images/day-heavy-snow-shower.svg',
        'images/night-heavy-shower.svg'
      ]);
    })
  );
 });

self.addEventListener('activate', event => {
  // delete any caches that aren't in expectedCaches
  event.waitUntil(
    caches.keys().then(keys => Promise.all(
      keys.map(key => {
        if (!expectedCaches.includes(key)) {
          return caches.delete(key);
        }
      })
    ))
  );
});

 // Need to decide on a file by file basis what the caching strategy is
self.addEventListener('fetch', function(e) {
  e.respondWith(
    caches.match(e.request).then(function(response) {
      return response || fetch(e.request);
    })
  );
});

//
self.addEventListener('push', function(e) {
  var body;

  if (e.data) {
    body = e.data.json().join(', ');
  } else {
    body = 'Push message no payload';
  }

  var options = {
    body: body,
    icon: 'android-chrome-192x192.png',
    badge: 'mono-72x72.png',
    vibrate: [100, 50, 100]
  };
  e.waitUntil(
    self.registration.showNotification('Hello!', options)
  );
});

self.addEventListener('notificationclick', function(e) {
  var notification = e.notification;
  // var primaryKey = notification.data.primaryKey;
  // var action = e.action;
  clients.openWindow('https://weather.rw251.com');
  notification.close();
});