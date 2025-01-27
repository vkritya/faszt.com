import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import usePHP from 'vite-plugin-php'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        vue(),
        usePHP(),
        tailwindcss()
    ],
    build: {
      rollupOptions: {
        output: {
          manualChunks: undefined,
        },
      },
    },
})