// Cola simple de revisiones offline en IndexedDB usando idb-keyval (opcional) o API nativa
// Aquí implementamos con IndexedDB nativa para no agregar dependencias

function openDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('AscensoresDB', 1)
    request.onerror = () => reject(request.error)
    request.onsuccess = () => resolve(request.result)
    request.onupgradeneeded = (e) => {
      const db = e.target.result
      if (!db.objectStoreNames.contains('revisiones-pendientes')) {
        const store = db.createObjectStore('revisiones-pendientes', { keyPath: 'temp_id' })
        store.createIndex('ascensor_id', 'ascensor_id', { unique: false })
        store.createIndex('fecha', 'fecha', { unique: false })
      }
    }
  })
}

async function putPending(item) {
  const db = await openDB()
  return new Promise((resolve, reject) => {
    const tx = db.transaction('revisiones-pendientes', 'readwrite')
    tx.objectStore('revisiones-pendientes').put(item)
    tx.oncomplete = () => resolve(true)
    tx.onerror = () => reject(tx.error)
  })
}

async function getAllPending() {
  const db = await openDB()
  return new Promise((resolve, reject) => {
    const tx = db.transaction('revisiones-pendientes', 'readonly')
    const req = tx.objectStore('revisiones-pendientes').getAll()
    req.onsuccess = () => resolve(req.result || [])
    req.onerror = () => reject(req.error)
  })
}

async function clearAll() {
  const db = await openDB()
  return new Promise((resolve, reject) => {
    const tx = db.transaction('revisiones-pendientes', 'readwrite')
    tx.objectStore('revisiones-pendientes').clear()
    tx.oncomplete = () => resolve(true)
    tx.onerror = () => reject(tx.error)
  })
}

export function useOfflineQueue() {
  async function enqueueRevision(revision) {
    const item = {
      ...revision,
      temp_id: revision.temp_id || crypto.randomUUID(),
      queued_at: new Date().toISOString(),
    }
    await putPending(item)
    return item.temp_id
  }

  async function syncAll() {
    const pendientes = await getAllPending()
    if (!pendientes.length) return { ok: true, count: 0 }

    // Usamos fetch con credenciales (cookies de sesión) y token CSRF
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    const resp = await fetch('/api/revisiones/sync', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token || '',
      },
      credentials: 'same-origin',
      body: JSON.stringify({ revisiones: pendientes }),
    })

    if (!resp.ok) {
      const text = await resp.text()
      throw new Error(text || 'Error al sincronizar')
    }

    await clearAll()
    return { ok: true, count: pendientes.length }
  }

  return {
    enqueueRevision,
    syncAll,
  }
}
