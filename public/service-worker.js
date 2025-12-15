self.addEventListener('install', () => {
  console.log('[SW] Instalado');
});

self.addEventListener('activate', () => {
  console.log('[SW] Activado');
});
