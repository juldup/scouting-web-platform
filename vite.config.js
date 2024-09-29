import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/styles.css',
                    'resources/js/application.js',
                    'resources/js/libs/bootstrap.min.js',
                    'js/libs/jquery-1.11.0.min.js',
                    'resources/js/libs/boostrap-switch.min.js',
                    'resources/js/libs/jquery.tablesorter.js',
                    'resources/fonts/glyphicons-halflings-regular.woff',
                    'resources/css/ckeditor/imageresize.css',
                   ],
            refresh: true,
        }),
    ],
});
