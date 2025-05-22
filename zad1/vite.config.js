import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      '/api': {
        target: 'http://sneakers-back.local',
        changeOrigin: true,
        secure: false,
        rewrite: (path) => path.replace(/^\/api/, '/api'),
        // Дополнительные настройки прокси
        configure: (proxy, _options) => {
          proxy.on('error', (err, _req, _res) => {
            console.log('Ошибка прокси:', err);
          });
          proxy.on('proxyReq', (proxyReq, req, _res) => {
            console.log('Запрос через прокси:', req.method, req.url);
          });
          proxy.on('proxyRes', (proxyRes, req, _res) => {
            console.log('Ответ от прокси:', proxyRes.statusCode, req.url);
          });
        }
      }
    }
  }
})
