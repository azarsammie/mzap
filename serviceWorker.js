// This is the service worker with the Cache-first network

const CACHE = "mzapcr-pwa-v20";
const precacheFiles = [
  '/',
  '/index.html',


  // ******[CSS]********
  '/style.css',
  '/css/animations.css',
  '/css/framework7.css',
  '/css/index.css',
  '/css/reset.css',
  '/css/swipebox.css',

// colors
  '/css/colors/blue.css',
  '/css/colors/grayscale.css',
  '/css/colors/magenta.css',
  '/css/colors/orange.css',
  '/css/colors/red.css',
  '/css/colors/turquoise.css',

  // Javascript
  'https://unpkg.com/jquery@3.4.1/dist/jquery.min.js',
  'https://unpkg.com/leaflet@1.5.1/dist/leaflet.css',
  'https://unpkg.com/leaflet@1.5.1/dist/leaflet.js',
  'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
  'https://unpkg.com/dexie@latest/dist/dexie.js',
  '/js/my-app.js',
  '/js/mzapcr.js',
  '/js/jquery-1.10.1.min.js',
  '/js/jquery.validate.min.js',
  '/js/jquery.swipebox.js',
  '/js/function.js',
  '/js/framework7.js',
  '/js/moment-timezone.js',

  // Images
  '/css/img/icons.png',
  '/css/img/icons.svg',
  '/css/img/loader.gif',
  '/images/add.png',
  '/images/bullet.png',
  '/images/camera.png',
  '/images/dropdown.png',
  '/images/flag.png',
  '/images/hello-icon-128.png',
  '/images/hello-icon-144.png',
  '/images/hello-icon-152.png',
  '/images/hello-icon-192.png',
  '/images/hello-icon-256.png',
  '/images/hello-icon-512.png',
  '/images/load_posts.png',
  '/images/load_posts_disabled.png',
  '/images/loader.gif',
  '/images/logo.png',
  '/images/logo_black.png',
  '/images/next.png',
  '/images/page_photo.jpg',
  '/images/prev.png',
  '/images/profile.jpg',
  '/images/profile_picture.png',
  '/images/switch_11.png',
  '/images/switch_11_active.png',
  '/images/switch_12.png',
  '/images/switch_13.png',
  '/images/switch_13_active.png',
  '/images/trans_black_gradient.png',
  '/images/icons/white/add.png',
  '/images/icons/white/back.png'
  

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
  event.respondWith(
    fromCache(event.request).then(
      function (response) {
//         // The response was found in the cache so we responde with it and update the entry
          console.log("Response found in cache:" +response);
//         // This is where we call the server to get the newest version of the
//         // file to use the next time we show view
        event.waitUntil(
          fetch(event.request).then(function (response) {
            return updateCache(event.request, response);
          })
        );

        return response;
      },
      function () {
        // The response was not found in the cache so we look for it on the server
        return fetch(event.request)
          .then(function (response) {
            // If request was success, add or update it in the cache
            event.waitUntil(updateCache(event.request, response.clone()));

            return response;
          })
          .catch(function (error) {
            console.log("Network request failed and no cache." + error);
          });
      }
    )
  );
  // event.respondWith(
  //   // Always try to download from server first
  //   fetch(event.request).then(response => {
  //     // When a download is successful cache the result
  //     caches.open(CACHE).then(cache => {
  //       cache.put(event.request, response)
  //     });
  //     // And of course display it
  //     return response.clone();
  //   }).catch((_err) => {
  //     // A failure probably means network access issues
  //     // See if we have a cached version
  //     return caches.match(event.request).then(cachedResponse => {
  //       if (cachedResponse) {
  //         // We did have a cached version, display it
  //         return cachedResponse;
  //       }

  //       // We did not have a cached version, display offline page
  //       // return caches.open(CACHE).then((cache) => {
  //       //   const offlineRequest = new Request(OFFLINE);
  //       //   return cache.match(offlineRequest);
  //       // });
  //     });
  //   })
  // );

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
