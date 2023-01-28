/** @type {import('tailwindcss').Config} */

// Values for screens (breakpoints).
const screens = {
  sm: '480px',
  md: '640px',
  lg: '960px',
  xl: '1280px',
}

// Values for color properties.
const colors = {
  transparent: 'transparent',
  white: '#ffffff',
  black: '#000000',
  green: {
    dark: '#2d3b1e',
    light: '#7d845f',
  },
  cream: '#ecdfc7',
  sienna: '#ca7339',
}

module.exports = {
  content: ["./templates/*/*.{html,twig}"],
  theme: {
    colors: colors,
    screens: screens,
    extend: {},
    fontSize: {
      sm: ['14px', '20px'],
      base: ['16px', '24px'],
      lg: ['20px', '28px'],
      xl: ['24px', '32px'],
      h1: ['70px', '91px'],
    },
    fontFamily: {
      'poppins': ['Poppins', 'ui-sans-serif', 'system-ui'],
    }
  },
  plugins: [],
}
