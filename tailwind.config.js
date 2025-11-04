module.exports = {
  purge: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: 'class', // enable dark mode toggle
  theme: {
    extend: {
      fontFamily: {
        poppins: ['Poppins', 'sans-serif'],
      },
      colors: {
        primary: '#2563eb',
        secondary: '#1e293b',
      },
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
