import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./Modules/**/*/resources/views/**/*.blade.php",
        "./Modules/**/*/resources/assets/js/**/*.{js,ts,vue}",
    ],

    theme: {
        extend: {
            borderRadius: {
                none: "0px",
                sm: "0.25rem", // was 0.125rem
                DEFAULT: "0.5rem", // was 0.25rem
                md: "0.75rem", // was 0.375rem
                lg: "1rem", // was 0.5rem
                xl: "1.5rem", // was 0.75rem
                "2xl": "2rem", // was 1rem
                "3xl": "3rem", // was 1.5rem
                full: "9999px",
            },
            colors: {
                primary: "#A0652E",
                offwhite: "#F6F6F5",
                dark: "#121211",
                accentRed: "#CB4137",
                lightPink: "#FFF1F3",
                darkGray: "#3E3E3B",

                yellow: {
                    400: "#facc15",
                    500: "#eab308",
                },
                red: {
                    400: "#f87171",
                    500: "#ef4444",
                },
            },
            boxShadow: {
                "soft-xs": "0px 2px 3px -5px #030C1312",
                "soft-sm": "0px 5px 6px -10px #030C1312",
                "soft-md": "0px 8px 12px -15px #030C1312",
                "hard-sm": "4px 8px 16px 0px #030C1312",
                "hard-md": "8px 16px 24px 0px #030C1312",
                "hard-lg": "16px 32px 40px 0px #030C1312",
                box: "1px 4px 8px 0px rgba(3, 12, 19, 0.07), 0px 1px 2px 0px rgba(3, 12, 19, 0.07)",
            },
            fontFamily: {
                iransans: ["IRANSansFaNum", "sans-serif"],
                lahzeh: {
                    thin: ["Lahzeh-Thin", "sans-serif"],
                    extralight: ["Lahzeh-ExtraLight", "sans-serif"],
                    light: ["Lahzeh-Light", "sans-serif"],
                    normal: ["Lahzeh-Regular", "sans-serif"],
                    medium: ["Lahzeh-Medium", "sans-serif"],
                    semibold: ["Lahzeh-SemiBold", "sans-serif"],
                    bold: ["Lahzeh-Bold", "sans-serif"],
                    extrabold: ["Lahzeh-ExtraBold", "sans-serif"],
                    black: ["Lahzeh-Black", "sans-serif"],
                },
            },
        },
    },


    plugins: [
        require("@tailwindcss/forms"),
        require("@toolwind/corner-shape"),
        function ({ addUtilities, theme }) {
            const radii = theme("borderRadius");
            const utilities = {};

            Object.keys(radii).forEach((key) => {
                // Skip 'full' and 'none' to keep them as standard Tailwind radius
                if (key === "full" || key === "none") return;

                const className =
                    key === "DEFAULT" ? ".rounded" : `.rounded-${key}`;
                utilities[className] = {
                    "corner-shape": "squircle",
                };
            });

            addUtilities(utilities);
        },
    ],
    safelist: [
        {
            pattern:
                /(bg|text)-(purple|yellow|green|red|blue|orange|cyan|indigo|pink|sky|emerald)-(50|100|700|800)/,
        },
        {
            pattern:
                /inset-ring-(purple|yellow|green|red|blue|orange|cyan|indigo|pink|sky|emerald)-(500|600|700)\/\d+/,
        },
    ],
};
