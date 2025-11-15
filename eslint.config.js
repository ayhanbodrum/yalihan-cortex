import js from '@eslint/js';

export default [
    js.configs.recommended,
    {
        files: ['resources/js/**/*.js'],
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: 'module',
            globals: {
                // Browser globals
                window: 'readonly',
                document: 'readonly',
                console: 'readonly',
                alert: 'readonly',
                fetch: 'readonly',
                FormData: 'readonly',
                localStorage: 'readonly',
                sessionStorage: 'readonly',
                navigator: 'readonly',
                setTimeout: 'readonly',
                setInterval: 'readonly',
                clearTimeout: 'readonly',
                clearInterval: 'readonly',
                // Alpine.js
                Alpine: 'readonly',
                // Laravel
                axios: 'readonly',
                route: 'readonly',
                // Google Maps
                google: 'readonly',
            },
        },
        rules: {
            // Kod Kalitesi
            'no-console': ['warn', { allow: ['warn', 'error'] }],
            'no-debugger': 'error',
            'no-alert': 'warn',
            'no-var': 'error',
            'prefer-const': 'error',
            'prefer-arrow-callback': 'warn',

            // Eşitlik
            eqeqeq: ['error', 'always'],

            // Best Practices
            'no-unused-vars': [
                'error',
                {
                    argsIgnorePattern: '^_',
                    varsIgnorePattern: '^_',
                },
            ],
            'no-undef': 'error',
            'no-redeclare': 'error',

            // Kod Stili
            semi: ['error', 'always'],
            quotes: ['error', 'single', { avoidEscape: true }],
            indent: ['error', 4],
            'comma-dangle': ['error', 'always-multiline'],
            'no-trailing-spaces': 'error',
            'eol-last': ['error', 'always'],

            // Modern JavaScript
            'no-prototype-builtins': 'off',
            'require-await': 'warn',
            'no-return-await': 'warn',
        },
    },
    {
        ignores: [
            'node_modules/**',
            'vendor/**',
            'public/build/**',
            'storage/**',
            'bootstrap/cache/**',
            '.husky/**',
        ],
    },
];
