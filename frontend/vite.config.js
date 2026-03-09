import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [react()],
    server: {
        historyApiFallback: true,
        proxy: {
            '/api': {
                target: 'https://exemplo.dev.br',
                changeOrigin: true,
                rewrite: (path) => path.replace(/^\/api/, '/endpoints')
            }
        }
    }
})
