document.addEventListener("DOMContentLoaded", () => {
  const menuIcon = document.getElementById("mobile-menu-icon");
  const mobileMenu = document.getElementById("mobile-menu");
  const searchIcon = document.getElementById("mobile-search-icon");
  const mobileSearchBar = document.querySelector(".mobile-search-bar");

  menuIcon.addEventListener("click", () => {
    mobileMenu.classList.toggle("show");
    menuIcon.classList.toggle("active");
  });

  searchIcon.addEventListener("click", () => {
    mobileSearchBar.classList.toggle("show");
    searchIcon.classList.toggle("active");

    const iconSpan = searchIcon.querySelector(".material-symbols-outlined");
    if (mobileSearchBar.classList.contains("show")) {
      iconSpan.textContent = "close"; // Ícone 'close' (um 'X')
      iconSpan.classList.add("rotated");
    } else {
      iconSpan.textContent = "search"; // Ícone 'search' de volta
      iconSpan.classList.remove("rotated");
    }
  });

  // --- Lógica para Links da Bottom-Navbar (Conta, Carrinho, etc.) ---
  const currentPath = window.location.pathname;
  const bottomNavLinks = document.querySelectorAll(".bottom-navbar li a");

  bottomNavLinks.forEach((link) => {
    const linkPath = link.getAttribute("href");

    const currentPageName = currentPath.split("/").pop();
    const linkPageName = linkPath.split("/").pop();

    if (linkPageName === currentPageName) {
      link.classList.add("active");
    }
    // Casos especiais para a página inicial, se a URL for apenas '/'
    else if (
      linkPageName === "index.php" &&
      (currentPath === "/" || currentPath.endsWith("index.php"))
    ) {
      link.classList.add("active");
    }
    // Caso específico para link de perfil/login que pode mudar
    else if (
      linkPageName === "user.php" &&
      (currentPageName === "user.php" || currentPageName === "profile.php")
    ) {
      link.classList.add("active");
    }
    // Se o link for para profile.php e a página atual for user.php ou profile.php
    else if (
      linkPageName === "profile.php" &&
      (currentPageName === "user.php" || currentPageName === "profile.php")
    ) {
      link.classList.add("active");
    }
    // Se você tiver um link de carrinho que leva a 'api_mercado_pago.php' em vez de 'cart.php'
    // e o link no HTML for 'cart.php'
    else if (
      linkPageName === "cart.php" &&
      (currentPageName === "cart.php" ||
        currentPageName === "api_mercado_pago.php")
    ) {
      link.classList.add("active");
    }
  });
});
