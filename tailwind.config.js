/** generate color palettes on tints.dev
 * to enforce shades consistency across themes*/
import preset from './vendor/filament/support/tailwind.config.preset'

module.exports = {
    presets: [preset],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php',
        './vendor/awcodes/filament-curator/resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                brand: ["Poppins", "sans-serif"],
                'brand-alt': ["Roboto", "sans-serif"],
            },
            colors: {
                brand: {
                    50: "#FFE0E2",
                    100: "#FFC2C5",
                    200: "#FF8A90",
                    300: "#FF4D55",
                    400: "#FF1420",
                    500: "#D8000C",
                    600: "#AD0009",
                    700: "#800006",
                    800: "#570004",
                    900: "#290002",
                    950: "#140001"
                },
                secondary: {
                    50: "#E3E7ED",
                    100: "#CAD1DD",
                    200: "#93A0B9",
                    300: "#607394",
                    400: "#3C485D",
                    500: "#1A1F28",
                    600: "#14181F",
                    700: "#101319",
                    800: "#0A0C0F",
                    900: "#060709",
                    950: "#020203"
                },
                shade: {
                    50: "#FCFCFD",
                    100: "#FCFCFD",
                    200: "#F5F6F9",
                    300: "#F2F3F7",
                    400: "#ECEDF4",
                    500: "#E9EBF2",
                    600: "#ACB3CE",
                    700: "#727EAC",
                    800: "#465177",
                    900: "#24293D",
                    950: "#11141D"
                },
                display: {
                    50: "#E3E7ED",
                    100: "#CAD1DD",
                    200: "#93A0B9",
                    300: "#607394",
                    400: "#3C485D",
                    500: "#1A1F28",
                    600: "#14181F",
                    700: "#101319",
                    800: "#0A0C0F",
                    900: "#060709",
                    950: "#020203"
                },
                accent: {
                    500: '#D2DFE8'
                }
            }
        }
    }
}
