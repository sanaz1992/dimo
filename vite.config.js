import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';


export default defineConfig({
    server: {
        host: '127.0.0.1',
        port: 5173,
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'Modules/Core/resources/assets/css/tailwind.css',
                'resources/js/app.js',
                'Modules/Core/resources/assets/js/utils.js',
                'Modules/Jetstream/resources/assets/css/login.css',
                'Modules/Jetstream/resources/assets/js/login.js',
                'Modules/Dashboard/resources/assets/css/index.css',
                'Modules/Shop/resources/assets/css/style.css',
                'Modules/Shop/resources/assets/css/products.css',
                'Modules/Shop/resources/assets/css/product-detail.css',

            ],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'Modules/Core/resources/assets/fonts/**/*',
                    dest: 'fonts'
                },
                {
                    src: 'Modules/Shop/resources/assets/fonts/**/*',
                    dest: 'fonts'
                },
                {
                    src: 'Modules/Core/resources/assets/images/*',
                    dest: 'images'
                },
                {
                    src: 'Modules/Core/resources/assets/plugins/persian-datepicker/*',
                    dest: 'plugins/persian-datepicker'
                },

            ],
            silent: true,
            failOnError: false
        }),
    ],
});
