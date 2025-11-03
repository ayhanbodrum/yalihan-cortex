/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.css",
        "./app/**/*.php",
    ],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                // Sadeleştirilmiş renk paleti - sadece gerekli renkler
                primary: {
                    500: "#f97316", // Orange 500 - Ana marka rengi
                    600: "#ea580c", // Orange 600 - Hover durumu
                },
                gray: {
                    50: "#f9fafb",
                    100: "#f3f4f6",
                    200: "#e5e7eb",
                    300: "#d1d5db",
                    500: "#6b7280",
                    600: "#4b5563",
                    700: "#374151",
                    800: "#1f2937",
                    900: "#111827",
                },
                green: {
                    500: "#22c55e", // Success
                    600: "#16a34a",
                },
                red: {
                    500: "#ef4444", // Danger
                    600: "#dc2626",
                },
                yellow: {
                    500: "#f59e0b", // Warning
                    600: "#d97706",
                },
                blue: {
                    500: "#0ea5e9", // Info
                    600: "#0284c7",
                },
            },
            fontFamily: {
                sans: ["Inter", "system-ui", "sans-serif"],
            },
            spacing: {
                // Sadece gerekli spacing değerleri
                18: "4.5rem",
            },
            borderRadius: {
                // Standart radius değerleri
                DEFAULT: "0.375rem", // 6px
                lg: "0.5rem", // 8px
                xl: "0.75rem", // 12px
            },
            boxShadow: {
                // Minimal shadow sistemi
                sm: "0 1px 2px 0 rgb(0 0 0 / 0.05)",
                DEFAULT:
                    "0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)",
                lg: "0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)",
            },
            transitionDuration: {
                DEFAULT: "200ms",
            },
        },
    },
    plugins: [],
};
