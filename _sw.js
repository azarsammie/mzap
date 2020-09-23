// var cacheName = 'mzapcr-pwa';
// var filesToCache = [
//   '/',
//   '/index.html',
//   '/css/style.css',
//   '/js/main.js'
// ];

// /* Start the service worker and cache all of the app's content */
// self.addEventListener('install', function(e) {
//   e.waitUntil(
//     caches.open(cacheName).then(function(cache) {
//       return cache.addAll(filesToCache);
//     })
//   );
// });

// /* Serve cached content when offline */
// self.addEventListener('fetch', function(e) {
//   e.respondWith(
//     caches.match(e.request).then(function(response) {
//       return response || fetch(e.request);
//     })
//   );
// });


// This is the service worker with the Cache-first network

const CACHE = "mzapcr-pwa";
const precacheFiles = [
  '/',
  '/index.html',
  '/css/style.css',
  '/js/main.js'
];

self.addEventListener("install", function (event) {
  console.log("Install Event processing");

  console.log("Skip waiting on install");
  self.skipWaiting();

  event.waitUntil(
    caches.open(CACHE).then(function (cache) {
      console.log("Caching pages during install");
      return cache.addAll(precacheFiles);
    })
  );
});

// Allow sw to control of current page
self.addEventListener("activate", function (event) {
  console.log("Claiming clients for current page");
  event.waitUntil(self.clients.claim());
});

// If any fetch fails, it will look for the request in the cache and serve it from there first


self.addEventListener("fetch", function (event) { 
  if (event.request.method !== "GET") return;
//   event.respondWith(
//     fromCache(event.request).then(
//       function (response) {
//         // The response was found in the cache so we responde with it and update the entry

//         // This is where we call the server to get the newest version of the
//         // file to use the next time we show view
        event.waitUntil(
          fetch(event.request).then(function (response) {
            return updateCache(event.request, response);
          })
        );

//         return response;
//       },
//       function () {
//         // The response was not found in the cache so we look for it on the server
//         return fetch(event.request)
//           .then(function (response) {
//             // If request was success, add or update it in the cache
//             event.waitUntil(updateCache(event.request, response.clone()));

//             return response;
//           })
//           .catch(function (error) {
//             console.log("Network request failed and no cache." + error);
//           });
//       }
//     )
//   );
  event.respondWith(
    // Always try to download from server first
    fetch(event.request).then(response => {
      // When a download is successful cache the result
      caches.open(CACHE).then(cache => {
        cache.put(event.request, response)
      });
      // And of course display it
      return response.clone();
    }).catch((_err) => {
      // A failure probably means network access issues
      // See if we have a cached version
      return caches.match(event.request).then(cachedResponse => {
        if (cachedResponse) {
          // We did have a cached version, display it
          return cachedResponse;
        }

        // We did not have a cached version, display offline page
        // return caches.open(CACHE).then((cache) => {
        //   const offlineRequest = new Request(OFFLINE);
        //   return cache.match(offlineRequest);
        // });
      });
    })
  );

});

function fromCache(request) {
  // Check to see if you have it in the cache
  // Return response
  // If not in the cache, then return
  return caches.open(CACHE).then(function (cache) {
    return cache.match(request).then(function (matching) {
      if (!matching || matching.status === 404) {
        return Promise.reject("no-match");
      }

      return matching;
    });
  });
}

function updateCache(request, response) {
  return caches.open(CACHE).then(function (cache) {
    return cache.put(request, response);
  });
}

// importScripts('https://cdn.onesignal.com/sdks/OneSignalSDKWorker.js');
