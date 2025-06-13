/* ====== Desktop and Mobile Menu ====== */

document.addEventListener("DOMContentLoaded", function () {
  // Десктопное меню
  const desktopUserButton = document.getElementById("desktop-user-button");
  const desktopUserMenu = document.getElementById("desktop-user-menu");
  
  // Мобильное sidebar меню
  const mobileMenuButton = document.getElementById("mobile-menu-button");
  const mobileMenu = document.getElementById("mobile-menu");
  const mobileMenuOverlay = document.getElementById("mobile-menu-overlay");
  const mobileMenuClose = document.getElementById("mobile-menu-close");

  // Функции для закрытия меню
  function closeDesktopMenu() {
    if (desktopUserMenu && !desktopUserMenu.classList.contains("twt-hidden")) {
      desktopUserMenu.classList.add("twt-hidden");
    }
  }

  function closeMobileSidebar() {
    if (mobileMenu) {
      mobileMenu.classList.remove("show");
    }
    if (mobileMenuOverlay) {
      mobileMenuOverlay.classList.remove("show");
    }
    // Возвращаем прокрутку страницы
    document.body.style.overflow = '';
  }

  function openMobileSidebar() {
    if (mobileMenu) {
      mobileMenu.classList.add("show");
    }
    if (mobileMenuOverlay) {
      mobileMenuOverlay.classList.add("show");
    }
    // Блокируем прокрутку страницы когда sidebar открыт
    document.body.style.overflow = 'hidden';
  }

  // Десктопное меню
  if (desktopUserButton) {
    desktopUserButton.addEventListener("click", function (event) {
      event.stopPropagation();
      desktopUserMenu.classList.toggle("twt-hidden");
    });
  }

  // Мобильное sidebar меню
  if (mobileMenuButton) {
    mobileMenuButton.addEventListener("click", function (event) {
      event.stopPropagation();
      openMobileSidebar();
    });
  }

  // Кнопка закрытия sidebar
  if (mobileMenuClose) {
    mobileMenuClose.addEventListener("click", function (event) {
      event.stopPropagation();
      closeMobileSidebar();
    });
  }

  // Закрытие sidebar при клике на overlay
  if (mobileMenuOverlay) {
    mobileMenuOverlay.addEventListener("click", function () {
      closeMobileSidebar();
    });
  }

  // Закрытие меню при клике вне области
  window.addEventListener("click", function (e) {
    // Проверяем десктопное меню
    if (desktopUserButton && desktopUserMenu) {
      const clickedInsideDesktop = desktopUserButton.contains(e.target) || desktopUserMenu.contains(e.target);
      if (!clickedInsideDesktop) {
        closeDesktopMenu();
      }
    }

    // Для мобильного sidebar используем overlay
  });

  // Закрытие меню при нажатии Escape
  document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") {
      closeDesktopMenu();
      closeMobileSidebar();
    }
  });

  // Закрытие мобильного меню при изменении размера экрана (если перешли на десктоп)
  window.addEventListener("resize", function() {
    if (window.innerWidth >= 768) { // md breakpoint
      closeMobileSidebar();
    }
  });

  // Предотвращение зума при двойном тапе на iOS
  let lastTouchEnd = 0;
  document.addEventListener('touchend', function (event) {
    const now = (new Date()).getTime();
    if (now - lastTouchEnd <= 300) {
      event.preventDefault();
    }
    lastTouchEnd = now;
  }, false);

  // Улучшение touch-событий для кнопок меню
  if ('ontouchstart' in window) {
    if (desktopUserButton) {
      desktopUserButton.addEventListener("touchstart", function(e) {
        // Не preventDefault для desktop меню
      }, { passive: true });
    }
    
    if (mobileMenuButton) {
      mobileMenuButton.addEventListener("touchstart", function(e) {
        // Не preventDefault для burger меню
      }, { passive: true });
    }
  }
});