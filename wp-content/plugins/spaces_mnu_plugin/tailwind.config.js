/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: "tw-",
  content: [
    "./**/*.php", // Учитывает все PHP файлы в проекте с любой вложенностью
    "./includes/**/*.js", // Учитывает все JavaScript файлы в папке src с любой вложенностью
    "./assets/**/*.js", // Учитывает все JavaScript файлы в папке berik с любой вложенностью
    "./**/*.js",
    "!./node_modules", // Исключает папку node_modules
    "!./node_modules/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'], // Устанавливает Inter как шрифт по умолчанию для sans
      },
    },
  },
};
