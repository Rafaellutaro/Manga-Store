document.querySelectorAll('.addToCart').forEach(cartButton => {
    cartButton.addEventListener('click', function (event) {
        event.preventDefault();
        const productId = this.getAttribute('data-product-id');
        
        
        
        

        fetch('cart.php?action=add&id=' + productId)
            .then(response => response.json())
            .then(data => {
                // Use the data received to update the UI or show a notification
                console.log(data);

            })
            .catch(error => {
                console.error(error);
            });
    });
});


