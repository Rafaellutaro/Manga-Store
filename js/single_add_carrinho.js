function addtocart_singlepage() {
    var productQuantity = document.getElementById("productQuantity").value;
    var quantityInt = parseInt(productQuantity, 10);
    var maxQuantity = parseInt(document.getElementById('stockQuantity').getAttribute('estoque'), 10);
    var productId = document.getElementById("addToCartButton").getAttribute("data-product-id");

    if (quantityInt > maxQuantity) {
        // notificação
        showToast('Quantidade solicitada maior que o estoque', 'error');
        return;
    }

    event.preventDefault();

    // Add AJAX call here to send productId and productQuantity to cart.php
    // You can use fetch or XMLHttpRequest to send the data

    // fetch:
    fetch(`cart.php?action=add&id=${productId}&quantity=${productQuantity}`)
        .then(response => response.text())
        .then(data => {
            console.log(data);
            
            // notificação
            showToast('Produto adicionado ao seu carrinho com sucesso', 'success');

        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erro ao adicionar o produto ao carrinho', 'error');
        });
}
