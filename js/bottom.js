document.addEventListener("DOMContentLoaded", function () {
  const toggles = document.querySelectorAll(".footer-toggle");

  toggles.forEach((toggle) => {
    toggle.addEventListener("click", function () {
      this.classList.toggle("active");
      const content = this.nextElementSibling;
      content.classList.toggle("open");
    });
  });
});
