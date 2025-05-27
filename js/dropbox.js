let debounceTimer;

const input = document.getElementById('live-search');
const suggestions = document.getElementById('search-suggestions');

input.addEventListener('keydown', (e) => {
  if (e.key === "Enter") {
    e.preventDefault();  // prevent form submit if inside a form
    const query = input.value.trim();

    if (query.length > 0) {
      // Redirect to shop.php with the search query as a GET param
      window.location.href = `shop.php?search=${encodeURIComponent(query)}`;
    }
  }
});

input.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    const query = this.value.trim();

    debounceTimer = setTimeout(() => {
        if (query.length < 2) {
            suggestions.innerHTML = '';
            suggestions.style.display = 'none';
            return;
        }

        fetch(`search.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                suggestions.innerHTML = '';

                if (data.length === 0) {
                    suggestions.style.display = 'none';
                    return;
                }

                data.forEach(item => {
                    const li = document.createElement('li');
                    li.classList.add('suggestion-item');

                    if (item.id && item.img && item.label && item.url) {
                        const img = document.createElement('img');
                        img.src = item.img;
                        img.width = 200;
                        img.height = 200;

                        img.classList.add('suggestion-img');

                        const span = document.createElement('span');
                        span.textContent = item.label;

                        li.appendChild(img);
                        li.appendChild(span);

                        li.addEventListener('click', () => {
                            window.location.href = `sproduct.php?url=${item.url}`;
                        });

                        suggestions.appendChild(li);
                    } else if (item.label && !item.url && !item.img && !item.id) {
                        const span = document.createElement('span');
                        span.textContent = item.label;
                        li.appendChild(span);
                        suggestions.appendChild(li);
                    }
                    
                });

                suggestions.style.display = 'block';
            })
            .catch(err => {
                console.error('Erro na busca:', err);
            });
    }, 300);
});

// Hide suggestions when clicking outside
document.addEventListener('click', function (e) {
    if (!e.target.closest('.search-wrapper')) {
        suggestions.innerHTML = '';
        suggestions.style.display = 'none';
    }
});