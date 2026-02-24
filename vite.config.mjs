import { defineConfig } from 'vite';
import manifestSRI from 'vite-plugin-manifest-sri';
import path from 'path';
import viteCompression from 'vite-plugin-compression';
import ViteRestart from 'vite-plugin-restart';

// https://vitejs.dev/config/
export default defineConfig(({command}) => ({
  base: command === 'serve' ? '' : '/dist/',
  build: {
    commonjsOptions: {
      transformMixedEsModules: true,
    },
    manifest: true,
    outDir: path.resolve(__dirname, 'dist/'),
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, 'src/js/app.js'),
        styles: path.resolve(__dirname, 'src/scss/app.scss'),
      },
    },
    sourcemap: true
  },
  plugins: [
    manifestSRI(),
    viteCompression({
      filter: /\.(js|mjs|json|css|map)$/i
    }),
    ViteRestart({
      reload: [
        'app/templates/**/*',
      ],
    }),
  ],
  publicDir: path.resolve(__dirname, 'src/public'),
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src'),
      '@scss': path.resolve(__dirname, 'src/scss'),
      '@js': path.resolve(__dirname, 'src/js'),
    },
  },
  server: {
    host: '0.0.0.0',
    port: 3000,
    strictPort: true,
    cors: true,
    origin: `${process.env.PRIMARY_SITE_URL?.replace(/\/$/, '')}:3000`,
  },
}));
