/* ====== User Menu ====== */

document.addEventListener("DOMContentLoaded", function () {
  const userMenuButton = document.getElementById("user-menu-button");
  const userMenu = document.getElementById("user-menu");

  function closeUserMenu() {
    if (userMenu && !userMenu.classList.contains("twt-hidden")) {
      userMenu.classList.add("twt-hidden");
    }
  }

  userMenuButton &&
    userMenuButton.addEventListener("click", function (event) {
      event.stopPropagation();
      userMenu.classList.toggle("twt-hidden");
    });

  window.addEventListener("click", function (e) {
    if (
      userMenuButton &&
      !userMenuButton.contains(e.target) &&
      !userMenu.contains(e.target)
    ) {
      closeUserMenu();
    }
  });
});
