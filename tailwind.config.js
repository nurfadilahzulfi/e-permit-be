// tailwind.config.js

/** @type {import('tailwindcss').Config} */
module.exports = {
  // [TAMBAHKAN BARIS INI]
  darkMode: 'class', 
  
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
        fontFamily: {
            // Pastikan 'Inter' ada di sini jika kamu menggunakannya
            inter: ['Inter', 'sans-serif'], 
        },
    },
  },
  plugins: [],
}