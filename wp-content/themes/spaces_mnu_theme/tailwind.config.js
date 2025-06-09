/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: "twt-",
  content: [
    "./*.php",
    "./template-parts/*.php",
    "./**/*.php",
    "./src/**/*.js",
    "./berik/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Mulish', 'sans-serif'],
      },
    },
  },
};
