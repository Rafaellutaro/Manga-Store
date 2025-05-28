function ativarBusca(input, suggestions = null) {
  let debounceTimer;

  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      const query = input.value.trim();
      if (query.length > 0) {
        window.location.href = `shop.php?search=${encodeURIComponent(query)}`;
      }
    }
  });

  input.addEventListener("input", function () {
    if (!suggestions) return;

    clearTimeout(debounceTimer);
    const query = this.value.trim();

    debounceTimer = setTimeout(() => {
      if (query.length < 2) {
        suggestions.innerHTML = "";
        suggestions.style.display = "none";
        return;
      }

      fetch(`search.php?query=${encodeURIComponent(query)}`)
        .then((response) => response.json())
        .then((data) => {
          suggestions.innerHTML = "";
          if (data.length === 0) {
            suggestions.style.display = "none";
            return;
          }

          data.forEach((item) => {
            const li = document.createElement("li");
            li.classList.add("suggestion-item");

            if (item.id && item.img && item.label && item.url) {
              const img = document.createElement("img");
              img.src = item.img;
              img.classList.add("suggestion-img");

              const span = document.createElement("span");
              span.textContent = item.label;

              li.appendChild(img);
              li.appendChild(span);

              li.addEventListener("click", () => {
                window.location.href = `sproduct.php?url=${item.url}`;
              });

              suggestions.appendChild(li);
            } else if (item.label) {
              const span = document.createElement("span");
              span.textContent = item.label;
              li.appendChild(span);
              suggestions.appendChild(li);
            }
          });

          suggestions.style.display = "block";
        });
    }, 300);
  });
}

// Pega os inputs e as caixas de sugestões
const desktopInput = document.getElementById("live-search");
const mobileInput = document.getElementById("mobile-search");
const desktopSuggestions = document.getElementById("search-suggestions");
const mobileSuggestions = document.getElementById("mobile-suggestions");

// Ativa busca com sugestões para desktop e mobile
ativarBusca(desktopInput, desktopSuggestions);
ativarBusca(mobileInput, mobileSuggestions);

// Esconde sugestões ao clicar fora
document.addEventListener("click", function (e) {
  if (!e.target.closest(".search-wrapper")) {
    desktopSuggestions.innerHTML = "";
    desktopSuggestions.style.display = "none";
  }

  if (!e.target.closest(".mobile-search-bar")) {
    mobileSuggestions.innerHTML = "";
    mobileSuggestions.style.display = "none";
  }
});
