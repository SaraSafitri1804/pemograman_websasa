/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./**/*.html",
    "./assets/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#4F46E5',
          container: '#4338CA',
          fixed: '#E0E7FF',
          'fixed-dim': '#C7D2FE',
        },
        secondary: {
          DEFAULT: '#C026D3',
          container: '#A21CAF',
          fixed: '#FAE8FF',
          'fixed-dim': '#F5D0FE',
        },
        tertiary: {
          DEFAULT: '#0D9488',
          container: '#0F766E',
        },
      },
      fontFamily: {
        body: ['Manrope', 'sans-serif'],
        label: ['Inter', 'sans-serif'],
      },
      boxShadow: {
        'glow': '0 0 20px rgba(79, 70, 229, 0.3)',
        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
      },
    },
  },
  plugins: [],
}

