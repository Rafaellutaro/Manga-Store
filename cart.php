<?php
include_once "connection.php";
include_once 'header.php';

if (isset($_GET['action']) && $_GET['action'] === 'update_quantidade' && isset($_GET['id']) && isset($_GET['quantity'])) {
    $productID = $_GET['id'];
    $newQuantity = $_GET['quantity'];

    $quantidade_int = (int) $newQuantity;
    // encontre o produto no carrinho e atualiza a quantidade
    foreach ($_SESSION['cart'] as $key => $cartItem) {
        if ($cartItem['id'] == $productID) {
            $_SESSION['cart'][$key]['quantity'] = $quantidade_int;
            break;
        }
    }
    // Return a response indicating the update was successful
    echo json_encode(['message' => 'Quantity updated successfully']);
}

if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $productID = $_GET['id'];
    $productQuantity = isset($_GET['quantity']) ? $_GET['quantity'] : 1;


    $productQuery = $conn->prepare("SELECT llx_product.*, llx_ecm_files.filepath, llx_ecm_files.filename
    FROM llx_product
    JOIN llx_ecm_files ON llx_product.rowid = llx_ecm_files.src_object_id WHERE llx_product.rowid = ?");

    // The bind_param method binds the parameters to the prepared statement. 

    $productQuery->bind_param("i", $productID);
    $productQuery->execute();
    $productResult = $productQuery->get_result();
    $product = $productResult->fetch_assoc();



    // Verifica se o carrinho está setado e dentro de um array
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        // Se não tiver setado ou não tiver dentro de um array, criar um array sem nada
        $_SESSION['cart'] = [];
    }

    // verifica se o produto já existe no carrinho
    $alreadyInCart = false;
    $productIDInt = (int) $productID;
    $productQuantity_int = (int) $productQuantity;

    foreach ($_SESSION['cart'] as $key => $cartItem) {
        $itemIDInt = (int) $cartItem['id'];
        error_log("Item ID: $itemIDInt, Product ID: $productIDInt, Already in Cart: $alreadyInCart");

        if ($itemIDInt === $productIDInt) {
            if ($cartItem['id'] == $productIDInt) {
                $alreadyInCart = true;
                $updatedQuantity = $_SESSION['cart'][$key]['quantity'] + $productQuantity_int;
                if ($updatedQuantity > $product['stock']) {
                    $updatedQuantity = $product['stock'];
                }
                $_SESSION['cart'][$key]['quantity'] = $updatedQuantity;
                break;
            }
        }
    }

    // vê se os produtos estão no carrinho
    if (!$alreadyInCart) {
        // adiciona ao carrinho
        $product['id'] = $productIDInt;
        $product['quantity'] = $productQuantity_int;
        $_SESSION['cart'][] = $product;
    }
}

// função de remover do carrinho
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $productID = $_GET['id'];

    // procura o index do produto dentro do array do carrinho
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $productID) {
            // Remove o produto do carrinho
            unset($_SESSION['cart'][$key]);
            header("Location: cart.php");
            break; // Quebra o looping depois de deletar
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/cart.css">
    <title>Cart</title>
</head>

<body>

    <section id="carrinho" class="section-p1">
        <h1>Carrinho de Compras</h1>
        <div class="cart-container">
            <?php
            // mostra os itens no carrinho
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $index => $cartItem) {
                    $img = "http://$dbhost/img/" . $cartItem["filepath"] . "/" . $cartItem["filename"];
                    // Usar identificadores unicos para cada produto
                    $productId = $cartItem['id'];
                    $productUrl = $cartItem['url'];
                    $productTotal = $cartItem['price'] * $cartItem['quantity'];
                    echo "<div class='cart-item'>";
                    echo "<a href='sproduct.php?url=" . $productUrl . "'>";
                    echo "<img src='" . $img . "' alt='Product Image' class='product-image'>";
                    echo "</a>";
                    echo "<div class='product-details'>";
                    echo "<h3 class='nome_manga'>" . $cartItem['label'] . "</h3>";
                    echo "<p>Preço: R$<span id='preco_produto_" . $productId . "'>" . $cartItem['price'] . "</span></p>";
                    echo "</div>";
                    echo "<div class='quantidade-container' >";
                    echo "<div id='quantidade_" . $productId . "' id_produto='" . $productId . "' estoque_qtd='" . $cartItem['stock'] . "' class='quantidade'>";
                    // mais, numero, menos
                    echo "<span class='menos' onclick='diminui_valor(" . $productId . ")'><i class='fas fa-minus'></i></span>";
                    echo "<input type='number' class='quantidade-input' id='quantidade_box_" . $productId . "' value='" . $cartItem['quantity'] . "' min='1' oninput='validar_box(this)' readonly>";
                    echo "<span class='mais' onclick='aumenta_valor(" . $productId . ")'><i class='fas fa-plus'></i></span>";
                    echo "</div>";
                    // botão deletar
                    echo "<form class='deletar_botao' id='deletar_botao_" . $productId . "' action='cart.php?action=remove&id=" . $productId . "' method='post'>";
                    echo "<a href='#' onclick='deletar_produto(" . $productId . ")' class='remove-button'>Remover produto</a>";
                    echo "</form>";
                    echo "</div>";
                    // Calculo total
                    echo "<p>Total: R$<span id='total_" . $productId . "'>" . $productTotal . "</span></p>";
                    echo "</div>";
                }
            } else {
                echo "<p>Seu carrinho está vazio.</p>";
            }
            ?>
        </div>
    </section>

    <section id="side-panel" class="section-p1">
        <div class="details">
            <h4>Resumo</h4>
            <span>Valor dos Produtos: R$0</span>
            <hr>
            <p>Frete: R$0</p>

        </div>

        <div class="details-delivery">
            <h4>Entrega</h4>
            <p>Endereço de entrega</p>
            <?php
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
            }

            // main query to get the address and zip code
            $query_main = "SELECT address, zip FROM llx_societe WHERE rowid = ?";
            $stmt_main = $conn->prepare($query_main);
            $stmt_main->bind_param("i", $user_id);
            $stmt_main->execute();
            $result_main = $stmt_main->get_result();

            // secondary query to get the address and zip code
            $query = "SELECT address, zip FROM llx_socpeople WHERE fk_soc = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <select>
                <?php
                if ($result->num_rows === 0 && $result_main->num_rows === 0) {
                    echo "<option selected disabled value=''>Nenhum endereço cadastrado</option>";
                }

                while ($row_main = $result_main->fetch_assoc()) {
                    echo "<option value='" . $row_main['address'] . "'>" . $row_main['address'] . " - " . $row_main['zip'] . "</option>";
                }

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['address'] . "'>" . $row['address'] . " - " . $row['zip'] . "</option>";
                }
                ?>
            </select>
            <p>Vendido e Entregue Por Manga Store</p>
            <hr>
            <p>Frete padrão: R$0</p>

            <a href="<?php echo $cartLink ?>">
                <button id="checkoutButton" name="checkoutButton">Finalizar compra</button>
            </a>
        </div>
    </section>

    <script src="js/carrinho_func.js"></script>
    <script>
        document.getElementById("checkoutButton").addEventListener("click", function(event) {
            <?php if (empty($_SESSION['cart'])) : ?>

                showToast("Seu carrinho está vazio", "error");
            <?php endif; ?>
        });
    </script>

</body>

</html>