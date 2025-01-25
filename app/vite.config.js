import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import usePHP from 'vite-plugin-php';

export default defineConfig({
    plugins: [
        vue(),
        usePHP()
    ],
    build: {
      rollupOptions: {
        output: {
          manualChunks: undefined,
        },
      },
    },
})