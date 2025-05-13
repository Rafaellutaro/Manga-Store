<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/AllDetails.css">
    <title>Detalhes da compra</title>
</head>

<body>
    <?php
    include_once "header.php";
    include_once "connection.php";

    $commandeId = $_POST['commandeId'];
    $fk_statut = $_POST['fk_statut'];
    ?>

    <div class="allProducts">
        <div class="container">
            <div class="sub_container">
                <div class="text">
                    <h2>Detalhes da compra</h2>
                    <p>Pedido: <?php echo $commandeId; ?></p>
                    <div class="text_container">
                        <?php
                        $amount_price = [];
                        $_SESSION['orderId'] = $commandeId;

                        $sql_product = "SELECT *
                                FROM llx_commandedet
                                WHERE llx_commandedet.fk_commande = $commandeId";
                        $result_product = $conn->query($sql_product);

                        while ($row_product = $result_product->fetch_assoc()) {
                            // id de todos os produtos comprados
                            $fk_product = $row_product["fk_product"];

                            $sql_product_final = "SELECT llx_product.*, llx_ecm_files.filepath, llx_ecm_files.filename
                                        FROM llx_product
                                        JOIN llx_ecm_files ON llx_product.rowid = llx_ecm_files.src_object_id
                                        WHERE llx_product.rowid = $fk_product";
                            $result_product_final = $conn->query($sql_product_final);

                            while ($row_product_final = $result_product_final->fetch_assoc()) {
                                //$img = "http://$dbhost/img/" . $row_product_final["filepath"] . "/" . $row_product_final["filename"];
                                $img = "https://" . $_SERVER['HTTP_HOST'] . "/img/" . $row_product_final["filepath"] . "/" . $row_product_final["filename"];
                                $url = $row_product_final["url"];
                                $nome = $row_product["label"];
                                $price = $row_product["subprice"];
                                $price = number_format($price, 2, ',', '.');
                                $qty = $row_product["qty"];

                                $amount_price[] = $price;

                                echo " <div class='product-entry-container'>";
                                echo "  <div class='product-entry'>";
                                echo "    <a href='sproduct.php?url=$url'>";
                                echo "      <img src='" . $img . "' alt='Product Image' class='product-image'>";
                                echo "    </a>";
                                echo "    <div class='product-details'>";
                                echo "      <div>Nome: $nome</div>";
                                echo "      <div>Preço: $price </div>";
                                echo "      <div>Quantidade: $qty</div>";
                                echo "    </div>";
                                echo "  </div>";
                                echo "</div>";
                                
                            }
                        }
                        $total = array_sum($amount_price);
                        $total = number_format($total, 2, ',', '.');
                        ?>
                    </div>
                    <hr>
                    <span>Total: <?= $total ?></span>
                    <?php
                        if ($fk_statut == "Pagamento Pendente"){
                            echo "<a href=api_mercado_pago_repay.php>";
                            echo    "<button>Pagar</button>";
                            echo "</a>";
                        }
                    ?>
                </div>
            </div>
        </div>

    </div>

</body>

</html>