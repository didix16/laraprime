import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
      './resources/js/**/*.{js,jsx,ts,tsx}',
        './resources/views/**/*.blade.php',

  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

