import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { obfuscatorPlugin, securityHeadersPlugin } from './vite-plugins/obfuscator.js';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Only apply obfuscation in production
        ...(process.env.NODE_ENV === 'production' ? [
            obfuscatorPlugin({
                // Custom obfuscation settings for Fotoku
                debugProtection: false,
                debugProtectionInterval: 2000,
                disableConsoleOutput: false,
                selfDefending: true,
                stringArray: true,
                stringArrayEncoding: ['base64'],
                controlFlowFlattening: true
            }),
            securityHeadersPlugin()
        ] : [])
    ],
    build: {
        rollupOptions: {
            output: {
                // Obfuscate chunk names in production
                chunkFileNames: process.env.NODE_ENV === 'production'
                    ? 'assets/[hash].js'
                    : 'assets/[name]-[hash].js',
                assetFileNames: process.env.NODE_ENV === 'production'
                    ? 'assets/[hash].[ext]'
                    : 'assets/[name]-[hash].[ext]'
            },
        },
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: process.env.NODE_ENV === 'production',
                drop_debugger: true,
                pure_funcs: process.env.NODE_ENV === 'production'
                    ? ['console.log', 'console.warn', 'console.error', 'console.info', 'console.debug']
                    : []
            },
            mangle: {
                properties: {
                    regex: /^_fotoku_/
                }
            },
            format: {
                comments: false
            }
        }
    },
    define: {
        __PRODUCTION__: JSON.stringify(process.env.NODE_ENV === 'production'),
        __DEVELOPMENT__: JSON.stringify(process.env.NODE_ENV !== 'production'),
        __APP_VERSION__: JSON.stringify(process.env.npm_package_version || '1.0.0')
    }
});
