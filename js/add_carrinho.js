document.querySelectorAll('.addToCart').forEach(cartButton => {
    cartButton.addEventListener('click', function (event) {
        event.preventDefault();
        var maxQuantity = parseInt(this.getAttribute('data-product-stock'), 10);
        var quantityInt = parseInt("1", 10);
        const productId = this.getAttribute('data-product-id');

        console.log("quantidade maxima: ", maxQuantity);
        console.log("quantidade solicitada: ", quantityInt);

        if (maxQuantity <=0) {
            // notificação
            showToast('Produto fora de estoque', 'error');
            return;
        }
        
        
        
        

        fetch('cart.php?action=add&id=' + productId)
            .then(response => response.text())
            .then(data => {
                // Use the data received to update the UI or show a notification
                showToast('Produto adicionado ao seu carrinho com sucesso', 'success');

            })
            .catch(error => {
                console.log(error);
                showToast('Erro ao adicionar o produto ao carrinho', 'error');
            });
    });
});


