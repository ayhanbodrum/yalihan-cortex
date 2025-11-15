/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.css',
        './app/**/*.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                primary: {
                    500: '#f97316',
                    600: '#ea580c',
                },
                gray: {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    500: '#6b7280',
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                },
                green: {
                    500: '#22c55e',
                    600: '#16a34a',
                },
                red: {
                    500: '#ef4444',
                    600: '#dc2626',
                },
                yellow: {
                    500: '#f59e0b',
                    600: '#d97706',
                },
                blue: {
                    500: '#0ea5e9',
                    600: '#0284c7',
                },
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [
        // Tailwind v4 uyumlu - @apply kullanmadan
        function ({ addComponents }) {
            addComponents({
                // Buttons
                '.neo-btn': {
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    gap: '0.5rem',
                    padding: '0.5rem 1rem',
                    fontWeight: '500',
                    fontSize: '0.875rem',
                    borderRadius: '0.5rem',
                    transition: 'all 200ms',
                },
                '.neo-btn-primary': {
                    background: 'linear-gradient(135deg, rgb(249 115 22), rgb(234 88 12))',
                    color: 'white',
                    boxShadow: '0 4px 6px rgba(0,0,0,0.1)',
                },
                '.neo-btn-secondary': {
                    backgroundColor: 'white',
                    color: 'rgb(55 65 81)',
                    border: '1px solid rgb(209 213 219)',
                },
                // Inputs
                '.neo-input': {
                    display: 'block',
                    width: '100%',
                    padding: '0.5rem 0.75rem',
                    border: '1px solid rgb(209 213 219)',
                    borderRadius: '0.5rem',
                    backgroundColor: 'white',
                    color: 'rgb(17 24 39)',
                },
                '.neo-label': {
                    display: 'block',
                    fontSize: '0.875rem',
                    fontWeight: '500',
                    color: 'rgb(55 65 81)',
                    marginBottom: '0.5rem',
                },
                // Cards
                '.neo-card': {
                    backgroundColor: 'white',
                    borderRadius: '0.75rem',
                    border: '1px solid rgb(229 231 235)',
                    boxShadow: '0 1px 2px rgba(0,0,0,0.05)',
                },
                '.neo-card-body': {
                    padding: '1.5rem',
                },
                // Legacy compatibility
                '.btn': {
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '0.5rem',
                    padding: '0.5rem 1rem',
                    fontWeight: '500',
                    fontSize: '0.875rem',
                    borderRadius: '0.5rem',
                    transition: 'all 200ms',
                },
                '.btn-primary': {
                    background: 'linear-gradient(135deg, rgb(249 115 22), rgb(234 88 12))',
                    color: 'white',
                },
                '.card': {
                    backgroundColor: 'white',
                    borderRadius: '0.75rem',
                    border: '1px solid rgb(229 231 235)',
                },
                '.form-control': {
                    display: 'block',
                    width: '100%',
                    padding: '0.5rem 0.75rem',
                    border: '1px solid rgb(209 213 219)',
                    borderRadius: '0.5rem',
                },
            });
        },
    ],
};
