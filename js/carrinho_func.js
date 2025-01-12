function aumenta_valor(productId) {
    var value = parseInt(document.getElementById('quantidade_box_' + productId).value, 10);
    value = isNaN(value) ? 1 : value;
    value++;
    document.getElementById('quantidade_box_' + productId).value = value;
    calcula_total(productId);
    calcularTotalGeral();
}

function diminui_valor(productId) {
    var value = parseInt(document.getElementById('quantidade_box_' + productId).value, 10);
    value = isNaN(value) ? 1 : value;
    value = value < 2 ? 1 : value - 1;
    document.getElementById('quantidade_box_' + productId).value = value;
    calcula_total(productId);
    calcularTotalGeral();
}

function deletar_produto(productId) {
    document.getElementById("deletar_botao_" + productId).submit();
}

function calcula_total(productId) {
    var price = parseFloat(document.getElementById('preco_produto_' + productId).innerText);
    var quantity = parseInt(document.getElementById('quantidade_box_' + productId).value);
    var total = price * quantity;
    document.getElementById('total_' + productId).innerText = total;

}

document.addEventListener('DOMContentLoaded', function () {
    var cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach(function (cartItem) {
        var productId = cartItem.querySelector('.quantidade-input').id.split('_').pop();
        var quantidadeBox = document.getElementById('quantidade_box_' + productId);
        quantidadeBox.addEventListener('input', function () {
            calcula_total(productId);
            
        });
        calcula_total(productId);
        calcularTotalGeral(); // Função calcular total
    });
    
});

document.querySelectorAll('.quantidade').forEach(container => {
    const productId = container.getAttribute('id_produto');
    const qtd = container.getAttribute('estoque_qtd');
    const quantidade = container.querySelector('.quantidade-input');
    const menos = container.querySelector('.menos');
    const mais = container.querySelector('.mais');

    menos.addEventListener('click', () => {
        if (parseInt(quantidade.value) >= 1) {
            let newQuantity = parseInt(quantidade.value);
            updateQuantity(productId, newQuantity);
        }
    });

    mais.addEventListener('click', () => {
        if (parseInt(quantidade.value) <= qtd) {
            let newQuantity = parseInt(quantidade.value);
            updateQuantity(productId, newQuantity);
        } else {
            alert("excedeu o limite do estoque");
            var price = parseFloat(document.getElementById('preco_produto_' + productId).innerText);
            document.getElementById('quantidade_box_' + productId).value = qtd;
            document.getElementById('total_' + productId).innerText = price * qtd;
            calcularTotalGeral();
            
        }

    });

    function updateQuantity(productId, newQuantity) {

        console.log("Product ID:", productId);
        console.log("New Quantity:", newQuantity);
        // Implement an AJAX request to update the quantity on the server
        fetch('cart.php?action=update_quantidade&id=' + productId + '&quantity=' + newQuantity, {
            method: 'GET'
        })
            .then(response => response.json())
            .then(data => {
                // Handle the response from the server, such as updating the UI
                console.log(data);
            })
            .catch(error => {
                console.error(error);
            });
    }
});

function calcularTotalGeral() {
    var totalGeral = 0;
    document.querySelectorAll('.cart-item').forEach(function (cartItem) {
        var productId = cartItem.querySelector('.quantidade-input').id.split('_').pop();
        var productTotal = parseFloat(cartItem.querySelector('#total_' + productId).innerText);
        totalGeral += productTotal;
    });
    document.querySelector('.details span').textContent = 'Total: R$' + totalGeral.toFixed(2);
}




