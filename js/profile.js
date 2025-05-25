document.addEventListener("DOMContentLoaded", function () {
  // Obter referências para os containers de detalhes
  const contaDetails = document.getElementById("contadetails");
  const enderecoDetails = document.getElementById("enderecodetails");
  const boughtDetails = document.getElementById("boughtProductsDetails");

  // Array de todas as seções de detalhes para fácil iteração
  const allDetailSections = [contaDetails, enderecoDetails, boughtDetails];

  // Obter referências para os rótulos da sidebar
  const contaLabel = document.getElementById("contalabel");
  const enderecoLabel = document.getElementById("enderecolabel");
  const boughtLabel = document.getElementById("boughtProductsLabel");

  // Array de todos os rótulos da sidebar
  const allLabels = [contaLabel, enderecoLabel, boughtLabel];

  // Função para mostrar uma seção específica e ocultar as outras
  function showSection(targetDetailsElement, targetLabelElement) {
    // 1. Ocultar todas as seções de detalhes e remover a classe 'active'
    allDetailSections.forEach((section) => {
      section.style.display = "none";
    });
    allLabels.forEach((label) => {
      label.classList.remove("active"); // Remove a classe 'active' de todos os rótulos
    });

    // 2. Exibir a seção de detalhes alvo e adicionar a classe 'active'
    if (targetDetailsElement) {
      targetDetailsElement.style.animation = "slideIn 0.3s ease-in-out";
      targetDetailsElement.style.display = "flex"; // Use 'flex' para corresponder ao seu CSS
      setTimeout(() => {
        targetDetailsElement.style.animation = "";
      }, 300);
    }

    // 3. Adicionar a classe 'active' ao rótulo clicado
    if (targetLabelElement) {
      targetLabelElement.classList.add("active");
    }
  }

  // Adicionar event listeners aos rótulos da sidebar
  contaLabel.addEventListener("click", () => {
    showSection(contaDetails, contaLabel);
  });

  enderecoLabel.addEventListener("click", () => {
    showSection(enderecoDetails, enderecoLabel);
  });

  boughtLabel.addEventListener("click", () => {
    showSection(boughtDetails, boughtLabel);
  });

  // Inicialmente, exibir a seção "Minha Conta" ao carregar a página
  // e marcar o rótulo correspondente como ativo
  showSection(contaDetails, contaLabel); // Inicia com "Minha Conta" aberta
});

const logoutLabel = document.getElementById("sair_label");

logoutLabel.addEventListener("click", () => {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "logout.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      window.location.href = "user.php"; // Ou a página de login/home
    }
  };
  xhr.send();
});
