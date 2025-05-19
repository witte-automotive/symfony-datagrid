import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import { glob } from "glob"
export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
            port: 5173,
        },
    },

    plugins: [
        symfonyPlugin(),
    ],

    build: {
        rollupOptions: {
            input: glob.sync('./assets/**/*.{js,ts,css}').reduce((entries, file) => {
                const name = file.replace(/^assets\/(.*)/, '$1').replace(/\.(js|ts|css)$/, '');
                entries[name] = file;
                return entries;
            }, {}),
        }
    },
});
