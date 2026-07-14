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
                    src: 'Modules/Core/resources/assets/icons/*',
                    dest: 'images/icons'
                },
                {
                    src: 'Modules/Core/resources/assets/images/*',
                    dest: 'images'
                },
                {
                    src: 'Modules/Core/resources/assets/plugins/persian-datepicker/*',
                    dest: 'plugins/persian-datepicker'
                },
                {
                    src: 'Modules/Core/resources/assets/plugins/sweetalert2/*',
                    dest: 'plugins/sweetalert2'
                },
                {
                    src: 'Modules/Warehouse/resources/assets/icons/sidebar/*',
                    dest: 'images/icons/sidebar'
                },
            ],
            silent: true,
            failOnError: false
        }),
    ],
});
