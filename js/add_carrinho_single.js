document.getElementById("addToCartButton").addEventListener("click", function() {
    const productId = "<?php echo $row['rowid']; ?>"; 
    const productQuantity = document.getElementById("productQuantity").value;
    // Send this data to your backend using an AJAX request or form submission
    // Example AJAX request using fetch:
    fetch("cart.php?action=add&id=" + productId + "&quantity=" + productQuantity)
        .then(response => response.json())
        .then(data => {
            // Handle the response data if needed
            console.log(data);

             //notificação
             var notification = document.createElement('div');
             notification.textContent = 'Item added to your cart';
             notification.style.position = 'fixed';
             notification.style.top = '18%';
             notification.style.left = '50%';
             notification.style.transform = 'translateX(-50%)';
             notification.style.padding = '10px';
             notification.style.backgroundColor = 'green';
             notification.style.color = 'white';
             notification.style.borderRadius = '5px';
             document.body.appendChild(notification);

             // timer

             setTimeout(function () {
                 notification.remove();
             }, 3000);
        })
        .catch(error => {
            // Handle any errors during the request
            console.error("Error:", error);
        });
});
