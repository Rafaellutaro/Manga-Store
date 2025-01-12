function addtocart_singlepage() {
    var productQuantity = document.getElementById("productQuantity").value;
    var quantityInt = parseInt(productQuantity, 10);
    var maxQuantity = parseInt(document.getElementById('stockQuantity').getAttribute('estoque'), 10);
    var productId = document.getElementById("addToCartButton").getAttribute("data-product-id");

    if (quantityInt > maxQuantity) {
        // notificação
        var notification = document.createElement('div');
            notification.textContent = 'Não há no estoque';
            notification.style.position = 'fixed';
            notification.style.top = '18%';
            notification.style.left = '50%';
            notification.style.transform = 'translateX(-50%)';
            notification.style.padding = '10px';
            notification.style.backgroundColor = '#ccc';
            notification.style.color = 'red';
            notification.style.borderRadius = '5px';
            document.body.appendChild(notification);

             //timer da notificação
            setTimeout(function () {
                notification.remove();
            }, 3000);
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
            var notification = document.createElement('div');
            notification.textContent = 'Produto adicionado ao seu carrinho com sucesso';
            notification.style.position = 'fixed';
            notification.style.top = '18%';
            notification.style.left = '50%';
            notification.style.transform = 'translateX(-50%)';
            notification.style.padding = '10px';
            notification.style.backgroundColor = '#ccc';
            notification.style.color = 'green';
            notification.style.borderRadius = '5px';
            document.body.appendChild(notification);

             //timer da notificação
            setTimeout(function () {
                notification.remove();
            }, 3000);

        })
        .catch(error => {
            console.error('Error:', error);
        });
}
