import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'


// https://vite.dev/config/
export default defineConfig({
  
  plugins: 
  [
    tailwindcss(),
    vue(),
  ],
  server: {
    host: true,
    port: 5173,
    proxy: {
      '/api': 
      {
        target: 'http://nginx',
        changeOrigin: true
      },
      '/ws': 
      {
        target: 'ws://websocket:2346',
        ws: true
      }
    }
  }
})
