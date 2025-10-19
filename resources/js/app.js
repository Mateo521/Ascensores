// resources/js/app.js
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
// Opción A: sin inertia-helpers, con glob eager y guard
createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const page = pages[`./Pages/${name}.vue`]
    if (!page) {
      console.error('Página Inertia no encontrada:', name)
      throw new Error(`No se encontró resources/js/Pages/${name}.vue. Verifica el nombre y mayúsculas/minúsculas.`)
    }
    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
  progress: { color: '#4B5563' },
})
