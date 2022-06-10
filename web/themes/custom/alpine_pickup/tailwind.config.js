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
  },
  plugins: [],
}
