document.addEventListener("DOMContentLoaded", function () {
  var preloader = document.getElementById("preloader");

  // Функции показа и скрытия прелоадера
  function showPreloader() {
    if (preloader) {
      preloader.style.display = "flex";
      preloader.style.opacity = "1";
    }
  }

  function hidePreloader() {
    if (preloader) {
      preloader.style.opacity = "0";
      setTimeout(function () {
        preloader.style.display = "none";
      }, 500);
    }
  }

  // Показ прелоадера при загрузке страницы
  window.addEventListener("beforeunload", function () {
    showPreloader(); // Показываем прелоадер при начале загрузки новой страницы или обновлении
  });

  window.addEventListener("load", function () {
    hidePreloader(); // Скрываем прелоадер, когда загрузка завершена
  });

  // // Глобальные обработчики AJAX с jQuery
  // jQuery(document).ajaxStart(function () {
  //   showPreloader(); // Показываем прелоадер при запуске любого AJAX-запроса
  // });

  // jQuery(document).ajaxStop(function () {
  //   hidePreloader(); // Скрываем прелоадер, когда все AJAX-запросы завершены
  // });

  // Таймер на случай, если что-то пошло не так (аварийное скрытие)
  setTimeout(function () {
    hidePreloader();
  }, 10000); // Скрыть прелоадер через 10 секунд
});
