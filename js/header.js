document.addEventListener("DOMContentLoaded", () => {
  const menuIcon = document.getElementById("mobile-menu-icon");
  const mobileMenu = document.getElementById("mobile-menu");
  const searchIcon = document.getElementById("mobile-search-icon");
  const mobileSearchBar = document.querySelector(".mobile-search-bar");

  menuIcon.addEventListener("click", () => {
    mobileMenu.classList.toggle("show");
  });

  searchIcon.addEventListener("click", () => {
    mobileSearchBar.classList.toggle("show");
  });
});
