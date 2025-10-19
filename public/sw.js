const CACHE_NAME = 'ascensores-app-v1'
const ASSETS = [
  '/', '/login',
  '/css/app.css',
  '/js/app.js',
]

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS))
  )
})

self.addEventListener('fetch', (event) => {
  const { request } = event
  // Estrategia: network first, fallback a cache
  event.respondWith(
    fetch(request).catch(() => caches.match(request))
  )
})
